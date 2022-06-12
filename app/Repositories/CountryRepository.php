<?php

namespace App\Repositories;

use App\Models\ {
    Country
};
use App\Traits\ {
    ExceptionHandlingTrait
};
use Illuminate\Http\Request;
use App\Libraries\IpAddress;
use App\Repositories\BaseRepository;

class CountryRepository extends BaseRepository
{

    use ExceptionHandlingTrait;

    /**
    * CountryRepository constructor.
    *
    * @param Country $model
    */
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Country::class;
    }

    public function datatable($request)
	{

        $data = $this->model->select('*');

        if($request['country_name']) {
            $data->where('country_name','like', '%'.$request['country_name'].'%');
        }

        if($request['country_code']) {
            $data->where('country_code', $request['country_code']);
        }

        if($request['country_isd_code']) {
            $data->where('country_isd_code', $request['country_isd_code']);
        }

        if($request['country_currency']) {
            $data->where('country_currency', $request['country_currency']);
        }

        if($request['currency_code']) {
            $data->where('currency_code', $request['currency_code']);
        }

        if($request['iso']) {
            $data->where('iso', $request['iso']);
        }

        if($request['country_status']) {
            $data->where('country_status', $request['country_status']);
        }

        if($request['currency_status']) {
            $data->where('currency_status', $request['currency_status']);
        }

        if($request['isCounter']) {
            return $data->count();
        }

        return $data->latest()->skip($request['start'])->take($request['length'])->get();
	}

    public function store($request) : array
    {
        try {

            $this->model->create($request->all());

            return [
                'error' => false,
                'message' => 'Data has been created.'
            ];

        } catch(\Exception $e) {
            return $this->showException($e->getMessage());
        }
    }

    public function update($request, $id) : array
    {
        try {

            $data = $this->model->find($id);

            $data->update([
                'country_name' => $request->country_name,
                'country_code' => $request->country_code,
                'currency_code' => $request->currency_code,
                'iso' => $request->iso,
                'currency_status' => $request->currency_status,
                'country_status' => $request->country_status,
                'country_isd_code' => $request->country_isd_code,
                'country_currency' => $request->country_currency
            ]);

            return [
                'error' => false,
                'message' => 'Data has been updated.'
            ];

        } catch(\Exception $e) {
            return $this->showException($e->getMessage());
        }
    }

    public function updateCurrencyStatus($request, $id) : array
    {
        try {

            $data = $this->model->find($id);

            $status = $data->currency_status == 'Active' ? 'Disabled' : 'Active';
            $data->update([
                'currency_status' => $status,
            ]);

            return [
                'error' => false,
                'message' => 'Data has been updated.'
            ];

        } catch(\Exception $e) {
            return $this->showException($e->getMessage());
        }
    }

    public function updateCountryStatus($request, $id) : array
    {
        try {

            $data = $this->model->find($id);

            $status = $data->country_status == 'Active' ? 'Disabled' : 'Active';

            $data->update([
                'country_status' => $status,
            ]);

            return [
                'error' => false,
                'message' => 'Data has been updated.'
            ];

        } catch(\Exception $e) {
            return $this->showException($e->getMessage());
        }
    }

    public function destroy($id) : array
    {
        try {

            $data = $this->model->find($id);

            if($data) {
                $data->delete();
            }

            return [
                'error' => false,
                'message' => 'Data has been deleted.'
            ];

        } catch(\Exception $e) {
            return $this->showException($e->getMessage());
        }
    }

    public function getDataByStatus($status = 'Active') : object
    {
        return $this->model->where('country_status', $status)->get();
    }

    public function status($status) : string
    {
        return $this->model::status[$status];
    }

    public function current() : string
    {
        return strtolower(IpAddress::ipInfo(NULL, 'countrycode'));
    }

}
