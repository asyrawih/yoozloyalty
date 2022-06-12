<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ {
    PaymentMethodRepository
};
use CommerceGuys\Intl\Currency\CurrencyRepository;

class PaymentMethodController extends Controller
{
    private $paymentMethod, $currency;

    public function __construct(
        PaymentMethodRepository $paymentMethod,
        CurrencyRepository $currency
    )
    {
        $this->paymentMethod = $paymentMethod;
        $this->currency = $currency;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->paymentMethod->datatable([
            'per_page' => $request->per_page,
            'service_name' => $request->service_name,
            'status' => $request->status
        ]);

        return $this->apiResponse([
            'error' => false,
            'message' => 'Payment Method lists.',
            'data' => $data,
            
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $locale = $request->query('locale', config('system.default_language'));

        app()->setlocale($locale);

        $currencies = $this->currency->getAll();

        $currencyData = [];

        foreach ($currencies as $currency_code => $currency) {
            $currencyData[] = array(
                'value' => $currency_code,
                'text' => "{$currency->getName()} ({$currency_code})"
            );
        }

        $data = json_decode(file_get_contents($this->paymentMethod->servicesBluePrint()), true);

        return $this->apiResponse([
            'error' => false,
            'message' => 'List Payment Method.',
            'data' => [
                'schema' => $data,
                'currencies' => $currencyData
            ]
        ]);
    }

    public function getApiScheme($name)
    {
        $schemes = [];

        if ($name !== 'null') {
            $schemes = json_decode(file_get_contents(str_replace('{file}', $name, $this->paymentMethod->serviceBluePrint())), true);
        }

        return $this->apiResponse([
            'error' => false,
            'message' => 'Detail Payment Method.',
            'data' => $schemes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->apiResponse($this->paymentMethod->store($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->apiResponse(
            $this->paymentMethod->update($request, $id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->apiResponse($this->paymentMethod->destroy($id));
    }
}
