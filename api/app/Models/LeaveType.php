<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'default_quota'];

    /**
     * Relasi ke LeaveBalances
     */
    public function balances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    /**
     * Relasi ke LeaveRequests
     */
    public function requests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}