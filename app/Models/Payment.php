<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = "payments";

    protected $fillable = [
        'id',
        'user_id',
        'match_id',
        'pandit_id',
        'merchant_transaction_id',
        'transaction_id',
        'payment_instrument',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'amount',
        'status',
        'created_at',
        'updated_at',
    ];

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function match() {
        return $this->hasOne(Matches::class, 'match_id', 'match_id');
    }

    public function pandit() {
        return $this->hasOne(Pandits::class, 'id', 'pandit_id');
    }
}
