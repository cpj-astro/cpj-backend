<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitors extends Model
{
    use HasFactory;

    protected $fillable = ['min', 'max', 'fake_users', 'status'];

    // Ensure only one record exists
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            // Check if there is an existing record
            if (static::count() > 1) {
                $model->delete(); // Delete the newly created record if there's more than one
            }
        });
    }
}
