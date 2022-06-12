<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsService extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'blueprint',
        'country_isd_code',
        'is_active',
        'schema'
    ];

}
