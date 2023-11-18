<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    use HasFactory;

    protected $table = "series"; 

    protected $fillable = [
        'series_id',
        'series_name',
        'series_date',
        'total_matches',
        'start_date',
        'end_date',
        'image',
        'month_wise',
        'source',
        'status'
    ];
}
