<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $leaveType;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->user = User::create([
            'name' => 'User Test',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $this->leaveType = LeaveType::create([
            'name' => 'Annual Leave',
            'default_quota' => 12
        ]);

        LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type_id' => $this->leaveType->id,
            'balance' => 12,
            'year' => date('Y')
        ]);
    }

    #[Test]
    public function user_cannot_request_leave_if_quota_is_insufficient()
    {
        // GANTI KE PATH MANUAL /api/...
        $response = $this->actingAs($this->user)
            ->postJson('/api/leave-requests', [
                'leave_type_id' => $this->leaveType->id,
                'start_date' => now()->addDays(1)->format('Y-m-d'),
                'end_date' => now()->addDays(15)->format('Y-m-d'),
                'reason' => 'Mau liburan panjang banget'
            ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Sisa kuota tidak mencukupi.']);
    }

    #[Test]
    public function user_cannot_request_overlap_dates()
    {
        // 1. Request pertama (Path Manual)
        $this->actingAs($this->user)->postJson('/api/leave-requests', [
            'leave_type_id' => $this->leaveType->id,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
            'reason' => 'Cuti Pertama'
        ]);

        // 2. Request kedua yang bentrok (Path Manual)
        $response = $this->actingAs($this->user)->postJson('/api/leave-requests', [
            'leave_type_id' => $this->leaveType->id,
            'start_date' => now()->addDays(6)->format('Y-m-d'),
            'end_date' => now()->addDays(8)->format('Y-m-d'),
            'reason' => 'Cuti Kedua'
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Tanggal bentrok dengan pengajuan aktif lainnya.']);
    }
}