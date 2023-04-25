<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $with = [
        'sedoAccount',
        'submittedByUser',
        'approvedByUser',
    ];

    protected $fillable = [
        'domain',
        'price',
        'registered',
        'submitted_by_user_id',
        'approved_by_user_id',
        'domain_registration_request_id',
        'sedo_account_id',
        'submitted_at',
        'registered_at',
        'approved_at',
    ];

    public function submittedByUser() {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function approvedByUser() {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function sedoAccount() {
        return $this->belongsTo(SedoAccount::class, 'sedo_account_id');
    }

    public function domainRegistrationRequest() {
        $this->hasOne(DomainRegistrationRequest::class, 'domain_registration_request_id');
    }
}
