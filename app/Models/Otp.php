<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $table = 'otp';

    static $statuses = [
        'pending' => "Pending",
        'used' => "Used",
        'expired' => "Expired",
        'invalid' => "Invalid"
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';
    const STATUS_INVALID = 'invalid';

    /**
     * This is where the OTP code is being generated.
     * This would also invalidate any previous code that may exist.
     */
    protected static function booted(){

        static::creating(function ($otp) {
            $past = self::latest()->first();
            if($past){
                $past->status = 'invalid';
                $past->save();
            }
            $otp->code = sprintf("%06d", mt_rand(1, 999999));
        });
    }

    /**
     * Generate OTP based on model and model id.
     *
     * @param $model
     * @param $model_id
     * @param $purpose
     * @param int $expiring_in defined in minutes.
     * @param string $via
     */
    public static function generateOtp($model, $model_id, $purpose, $expiring_in = 5, $via = 'email'){
        $otp = new self();
        $otp->model = $model;
        $otp->model_id = $model_id;
        $otp->purpose = $purpose;
        $otp->via = $via;
        $otp->expired_at = Carbon::now()->addMinutes($expiring_in);
        $otp->save();

        return $otp;
    }

    /**
     * Validate OTP code.
     *
     * @param $model
     * @param $model_id
     * @param $code
     * @return mixed
     */
    public static function validate($model, $model_id, $code){
        $otp = self::where('model', $model)->where('model_id', $model_id)->where('code', $code)->where('status', self::STATUS_PENDING)->first();
        if($otp){
            $otp->status = self::STATUS_USED;
            $otp->save();
            return true;
        }
        return false;
    }

    /**
     * Check if OTP is expired.
     * @return bool
     */
    public function isExpired(){
        return Carbon::now()->gt($this->expired_at);
    }

    public function model(){
        return $this->belongsTo($this->model, 'model_id', 'id');
    }
}
