<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $with = [
        'sedoAccount',
        'requestedByUser',
        'approvedByUser',
    ];

    protected $fillable = [
        'domain',
        'registered',
        'requested_by_user_id',
        'approved_by_user_id',
        'sedo_account_id',
    ];

    public function requestedByUser() {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function approvedByUser() {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function sedoAccount() {
        return $this->belongsTo(SedoAccount::class, 'sedo_account_id');
    }
}
