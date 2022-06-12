<?php

namespace App\Contracts\Merchant;

use App\Contracts\BaseRepositoryInterface;

interface BillingRepositoryInterface extends BaseRepositoryInterface
{
    public function checkout(array $payload): array;

    public function confirm(array $payload): array;

    public function cancel(array $payload): array;
}
