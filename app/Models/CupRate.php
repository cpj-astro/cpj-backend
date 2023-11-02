<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CupRate extends Model
{
    use HasFactory;

    protected $table = "cup_rates";

    protected $fillable = [
        'id', 'title', 'sub_title', 'sequence_number', 'status'
    ];

    public function cupRatesTeams(){
        $data =  $this->hasMany( CupRateTeams::class, 'cup_rate_id')->where('status', 1);
        return $data;
    }

    public function getAllCupRatesTeams(){
        $data =  $this->hasMany( CupRateTeams::class, 'cup_rate_id');
        return $data;
    }
}
