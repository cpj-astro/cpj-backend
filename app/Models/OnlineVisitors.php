<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineVisitors extends Model
{
    use HasFactory;
    protected $table = "online_visitors"; 

    protected $fillable = [
        'user_id','user_ip','status'
    ];
}
