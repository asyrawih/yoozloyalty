<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $currencies = new ISOCurrencies();
        $money = new Money($this->price, new Currency($this->currency_code ?? 'TTD'));

        $numberFormatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'price' => $money,
            'price_formatted' => $moneyFormatter->format($money),
            'billing_interval' => $this->billing_interval,
            'limitations_customers' => $this->sanitizeLimitation($this->limitations['customers']),
            'limitations_campaigns' => $this->limitations['campaigns'],
            'limitations_rewards' => $this->sanitizeLimitation($this->limitations['rewards']),
            'limitations_businesses' => $this->sanitizeLimitation($this->limitations['businesses']),
            'limitations_staff' => $this->sanitizeLimitation($this->limitations['staff']),
            'limitations_segments' => $this->sanitizeLimitation($this->limitations['segments']),
            'merchants_count' => $this->user_count,
        ];
    }

    private function sanitizeLimitation($value)
    {
        if (is_string($value) && strtolower(strval($value)) === 'unlimited') {
            return 'Unlimited';
        }

        return $value;
    }
}
