<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;

class NotifTemplate extends Model
{
    use HasFactory;
    use GeneratesUuid;

    protected $guarded = [];
}
