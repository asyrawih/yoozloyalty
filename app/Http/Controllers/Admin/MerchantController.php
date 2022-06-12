<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDropdownMerchant()
    {
        // $data = User::where('role',3)->select(['name as text', 'id as value'])->get();
        $merchants = User::query()->where('role', 3)->get();

        $items = $merchants->map(fn($merchant) => [
            'text' => $merchant->name,
            'value' => $merchant->id,
        ])->toArray();

        return $this->apiResponse([
            'error' => false,
            'message' => 'Merchant Lists',
            'data' => $items,

        ]);
    }
}
