<?php

namespace App\Http\Controllers\Merchant;

use App\Customer;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDropdownCustomer()
    {
        $customers = Customer::query()->where('created_by', auth()->id())->get();

        $items = $customers->map(fn($customer) => ['text' => $customer->name, 'value' => $customer->id])->toArray();

        return $this->apiResponse([
            'error' => false,
            'message' => 'Customer Lists',
            'data' => $items,

        ]);
    }
}
