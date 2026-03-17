<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    /**
     * User: Lihat riwayat cuti sendiri
     * Admin: Lihat semua riwayat (bisa difilter via route berbeda di api.php)
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['leaveType', 'user']);

        // Jika bukan admin, hanya tampilkan milik sendiri
        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    /**
     * User: Cek sisa kuota
     */
    public function getBalances(Request $request)
    {
        $balances = LeaveBalance::with('leaveType')
            ->where('user_id', $request->user()->id)
            ->get();
        return response()->json($balances);
    }

    /**
     * User: Ajukan Cuti
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $totalDays = $start->diffInDays($end) + 1;

        // 1. Quota Check
        $balance = LeaveBalance::where('user_id', $user->id)
            ->where('leave_type_id', $request->leave_type_id)
            ->first();

        if (!$balance || $balance->balance < $totalDays) {
            return response()->json(['message' => 'Sisa kuota tidak mencukupi.'], 422);
        }

        // 2. Overlap Detection (Sesuai Guide: tidak boleh bentrok dengan pending/approved)
        $overlap = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($request) {
                $query->where('start_date', '<=', $request->end_date)
                    ->where('end_date', '>=', $request->start_date);
            })->exists();

        if ($overlap) {
            return response()->json(['message' => 'Tanggal bentrok dengan pengajuan aktif lainnya.'], 422);
        }

        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Pengajuan berhasil dikirim.', 'data' => $leaveRequest]);
    }

    /**
     * Admin: Approve Request
     */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return response()->json(['message' => 'Hanya pengajuan pending yang bisa diproses.'], 422);
        }

        DB::transaction(function () use ($leaveRequest, $request) {
            // Kurangi saldo otomatis saat approve
            $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->lockForUpdate()
                ->first();
                
            $balance->decrement('balance', $leaveRequest->total_days);

            $leaveRequest->update([
                'status' => 'approved',
                'responded_by' => $request->user()->id,
                'responded_at' => now()
            ]);
        });

        return response()->json(['message' => 'Pengajuan disetujui.']);
    }

    /**
     * Admin: Reject Request
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return response()->json(['message' => 'Hanya pengajuan pending yang bisa diproses.'], 422);
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'responded_by' => $request->user()->id,
            'responded_at' => now()
        ]);

        return response()->json(['message' => 'Pengajuan ditolak.']);
    }

    /**
     * User: Cancel Request (Hanya jika pending)
     */
    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($leaveRequest->status !== 'pending') {
            return response()->json(['message' => 'Hanya status pending yang bisa dicancel.'], 422);
        }

        $leaveRequest->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Pengajuan berhasil dibatalkan.']);
    }

    /**
     * Shared: Soft Delete
     */
    public function destroy(Request $request, LeaveRequest $leaveRequest)
    {
        $user = $request->user();

        // Admin: bisa delete jika status FINAL (approved, rejected, cancelled)
        if ($user->role === 'admin') {
            if ($leaveRequest->status === 'pending') {
                return response()->json(['message' => 'Request pending tidak bisa dihapus, harus dicancel dulu.'], 422);
            }
        } 
        // User: hanya miliknya sendiri & status cancelled/rejected
        else {
            if ($leaveRequest->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            if (!in_array($leaveRequest->status, ['cancelled', 'rejected'])) {
                return response()->json(['message' => 'Hanya bisa menghapus request yang cancelled atau rejected.'], 422);
            }
        }

        $leaveRequest->update(['deleted_by' => $user->id]);
        $leaveRequest->delete(); // Menggunakan SoftDeletes trait

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}