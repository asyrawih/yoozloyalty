<?php

namespace App\Models;

use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Model;
use Platform\Models\Campaign;

class SmtpService extends Model
{
    use GeneratesUuid;

    protected $fillable = [
        'smtp_name',
        'mail_from_name',
        'mail_from_address',
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'is_active',
    ];

    /**
     * Field mutators.
     *
     * @var array
     */
    protected $casts = [
        'uuid' => EfficientUuid::class,
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        // On update
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->id;
            }
        });

        // On create
        self::creating(function ($model) {
            if (auth()->check()) {
                $model->account_id = auth()->user()->account_id;

                $model->created_by = auth()->user()->id;
            }
        });
    }

    // Relationship
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'smtp_service_id', 'id');
    }
}
