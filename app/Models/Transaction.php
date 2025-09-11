<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id','performed_by_user_id','type','amount','balance_after',
        'reference','counterparty','status','related_transaction_id','metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    public function related()
    {
        return $this->belongsTo(Transaction::class, 'related_transaction_id');
    }
}
