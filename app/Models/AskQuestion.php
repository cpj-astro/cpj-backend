<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AskQuestion extends Model
{
    use HasFactory;

    protected $table = "ask_question";

    protected $fillable = [
        'user_id',
        'wtsp_number',
        'question',
        'is_wtsp_number',
        'status',
        'answer',
        'transaction_id',
        'merchant_transaction_id',
        'amount',
        'payment_instrument',
    ];

    protected $casts = [
        'is_wtsp_number' => 'boolean',
        'status' => 'boolean',
    ];
}
