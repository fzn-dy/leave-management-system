<?php

namespace App\Observers;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;

class UserObserver
{
    public function created(User $user): void
    {
        // Hanya berikan balance jika role-nya adalah 'user'
        if ($user->role === 'user') {
            $leaveTypes = LeaveType::all();
            foreach ($leaveTypes as $type) {
                LeaveBalance::create([
                    'user_id' => $user->id,
                    'leave_type_id' => $type->id,
                    'year' => date('Y'),
                    'balance' => $type->default_quota,
                ]);
            }
        }
    }
}