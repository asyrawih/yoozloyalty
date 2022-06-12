<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\PointsExpiryFactory;

class PointsExpiry extends Model
{
    use HasFactory;

    protected $fillable = ['merchant_user_id', 'points_expiry'];
    
    /**
     * Create a new factory instance for the PointsExpiry model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
      return PointsExpiryFactory::new();
    }
}
