<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/*
 * Countries for users with relation to country's currency
 */
class Country extends Model
{

	protected $table = 'countries';

	protected $fillable = [
		'currency_id',
		'country_name',
		'country_code',
		'currency_code',
		'currency_status',
		'iso',
		'country_status',
		'country_isd_code',
		'country_currency'
	];

	const status = ['Disabled','Active'];

	const currency_status = ['Disabled','Active'];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

	public function states()
	{
		return $this->hasMany(State::class);
	}

	public function cities()
    {
		return $this->hasManyThrough(City::class,State::class);
    }

}
