<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;

class SettingLegal extends Model
{
    use HasFactory;
    use GeneratesUuid;

    protected $guarded = [];

    protected $fillable = [
        'content',
        'user_id',
        'type'
    ];

    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
