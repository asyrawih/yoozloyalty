<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationServices extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'blueprint',
        'is_active',
        'schema'
    ];
}
