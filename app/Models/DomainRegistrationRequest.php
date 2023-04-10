<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainRegistrationRequest extends Model
{
    use HasFactory;

    protected $with = [
        'domains'
    ];

    protected $fillable = [
        'submitted_by_user_id',
        'approved_by_user_id',
        'sedo_account_id',
    ];

    public function domains() {
        return $this->hasMany(Domain::class);
    }

    public function submittedByUser() {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function approvedByUser() {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function sedoAccount() {
        return $this->belongsTo(SedoAccount::class, 'sedo_account_id');
    }
}
