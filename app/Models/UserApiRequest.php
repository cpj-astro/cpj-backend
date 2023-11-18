<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserApiRequest extends Model
{
    use HasFactory;

    protected $table = "user_api_request"; 

    protected $fillable = ["user_id","request_counter","request_ip","request_token"];
}
