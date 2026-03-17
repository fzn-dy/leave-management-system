<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasFactory;

    // Sesuai dengan apa yang kamu miliki, sudah benar untuk mass assignment
    protected $fillable = [
        'user_id', 
        'leave_type_id', 
        'year', 
        'balance'
    ];

    /**
     * Relasi ke User (Karyawan pemilik kuota)
     * Penting: Digunakan saat Admin ingin melihat sisa cuti per user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Tipe Cuti
     * Digunakan untuk menampilkan nama kategori cuti di UI (misal: "Tahunan", "Sakit").
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
}