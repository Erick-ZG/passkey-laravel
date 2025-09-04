<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuthMetric extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id','kind','method','flow_state','workos_user_id','email',
        'started_at','ended_at','duration_ms','success','attempt','attempts_total',
        'error_code','error_message','context'
    ];
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'success'    => 'boolean',
        'context'    => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($m) {
            $m->id ??= (string) Str::uuid();
        });
    }
}
