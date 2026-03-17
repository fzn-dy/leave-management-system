<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Energeek',
            'email' => 'admin@energeek.co.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $user = User::create([
            'name' => 'Fauzan Developer',
            'email' => 'user@energeek.co.id',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
        $types = [
            ['name' => 'Annual Leave', 'default_quota' => 12],
            ['name' => 'Sick Leave', 'default_quota' => 6],
        ];

        foreach ($types as $typeData) {
            $type = LeaveType::create($typeData);
            LeaveBalance::create([
                'user_id' => $user->id,
                'leave_type_id' => $type->id,
                'year' => 2026,
                'balance' => $type->default_quota,
            ]);
        }
    }
}