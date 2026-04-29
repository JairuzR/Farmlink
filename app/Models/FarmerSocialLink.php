<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmerSocialLink extends Model
{
    protected $fillable = ['user_id', 'platform', 'label', 'url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}