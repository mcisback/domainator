<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FtpServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocol',
        'name',
        'url',
        'host',
        'port',
        'username',
        'password',
        'privateKey',
        'passive',
        'ssl',
        'usePrivateKey',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
