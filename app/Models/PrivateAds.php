<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateAds extends Model
{
    use HasFactory;

    protected $table = "private_ads"; 

    protected $fillable = [
        'title','media_file','ad_type','status','link','category','expiry_date','user_id'
    ];

    // public function getFormattedExpiryDateAttribute(){
    //     return Carbon::parse($this->expiry_date)->format('d-m-y');
    // }
    // public function setDateAttribute() {
    // }
}
