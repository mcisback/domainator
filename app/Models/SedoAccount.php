<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SedoAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'password',
        'partner_id',
        'signkey',
        'domain_ownership_id',
    ];
}
