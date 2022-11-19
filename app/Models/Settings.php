<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $casts = [
        'value' => 'array'
    ];

    public static function get(string $key) {

        return static::where('key', $key)->first()->value;

    }

    public static function set(string $key, array $value) {

        return static::upsert([
            [
                'key' => $key,
                'value' => json_encode($value),
            ],
        ], ['key'], ['value']);

    }
}
