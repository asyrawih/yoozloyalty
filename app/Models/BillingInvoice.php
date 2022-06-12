<?php

namespace App\Models;

use App\User;
use Money\Money;
use Money\Currency;
use NumberFormatter;
use Platform\Models\Plan;
use App\Models\Bank;
use Spatie\MediaLibrary\HasMedia;
use Money\Currencies\ISOCurrencies;
use Illuminate\Database\Eloquent\Model;
use Money\Formatter\IntlMoneyFormatter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\BillingInvoiceFactory;

class BillingInvoice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const PENDING = 'pending';
    const CONFIRM = 'confirm';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const CANCELED = 'canceled';

    const DEFAULT_CURRENCY = 'TTD';

    protected $fillable = [
        'user_id',
        'merchant_name',
        'order_id',
        'plan_id',
        'merchant_name',
        'previous_plan_name',
        'plan_name',
        'payment_method',
        'bank_id',
        'currency_code',
        'amount',
        'amount_paid',
        'merchant_bank_name',
        'merchant_identifier',
        'status',
        'paid_at',
        'expired_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expired_at' => 'datetime'
    ];

    protected $appends = [
        'currency_amount',
        'amount_formatted',
        'amount_paid_formatted',
        'receipt_url',
        'expired_at_formatted'
    ];

    /**
     * Create a new factory instance for the BillingInvoice model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
      return BillingInvoiceFactory::new();
    }

    public function getExpiredAtFormattedAttribute()
    {
        return $this->expired_at ? $this->expired_at->toDayDateTimeString() : null;
    }

    public function getAmountFormattedAttribute()
    {
        $money = new Money($this->amount * 100, new Currency($this->currency_code));

        $currencies = new ISOCurrencies();

        $numberFormatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        return $moneyFormatter->format($money);
    }

    public function getAmountPaidFormattedAttribute()
    {
        $money = new Money($this->amount_paid * 100, new Currency($this->currency_code));

        $currencies = new ISOCurrencies();

        $numberFormatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        return $moneyFormatter->format($money);
    }

    public function getCurrencyAmountAttribute()
    {
        return $this->currency_code;
    }

    public function getReceiptUrlAttribute()
    {
        if ($this->getFirstMediaUrl('receipt')) {
            return $this->getFirstMediaUrl('receipt', 'receipt_url');
        }

        return null;
    }

    public function scopeWhereUser(Builder $query, $user_id): Builder
    {
        return $query->where('user_id', $user_id);
    }

    public function scopeWhereOrder(Builder $query, $order_id): Builder
    {
        return $query->where('order_id', $order_id);
    }

    public function scopeWherePlan(Builder $query, $plan_id): Builder
    {
        return $query->where('plan_id', $plan_id);
    }

    public function scopeWhereUnpaid(Builder $query): Builder
    {
        return $query->where('status', 'unpaid');
    }

    public function scopeWherePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
