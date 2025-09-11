<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','account_id','merchant_id','amount','description','transaction_id','status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function account() { return $this->belongsTo(Account::class); }
    public function merchant() { return $this->belongsTo(Merchant::class); }
    public function transaction() { return $this->belongsTo(Transaction::class); }
}
