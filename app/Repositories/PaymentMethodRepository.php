<?php

namespace App\Repositories;

use App\Models\ {
    PaymentMethod
};

class PaymentMethodRepository extends BaseRepository
{

    /**
    * NotificationServiceRepository constructor.
    *
    * @param PaymentMethod $model
    */
    public function __construct(PaymentMethod $model)
    {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaymentMethod::class;
    }

    public function datatable($request)
    {
        $data = $this->model()::select('*');

        if (isset($request['service_name'])) {
            $data->where(
                'name',
                'like',
                '%'.$request['service_name'].'%'
            );
        }

        if (
            isset($request['status']) &&
            $request['status'] != ''
        ) {
            $data->where('is_active', $request['status']);
        }

        return $data->paginate($request['per_page']);
    }

    public function servicesBluePrint() : string
    {
        return storage_path('json/settings/payment-method/services.json');
    }

    public function serviceBluePrint() : string
    {
        return storage_path('json/settings/payment-method/schema/{file}.json');
    }

    public function store($request) : array
    {
        $serviceName = $request->service_name;

        /*
        * Check double data
        */
        $count = $this->checkDoubleData($serviceName);

        if ($count > 0) {
            return [
                'error' => true,
                'message' => $serviceName.' service have been already exists.'
            ];
        }

        $this->model()::create([
            'name' => str_replace('_', ' ', strtoupper($serviceName)),
            'blueprint' => strtolower($serviceName),
            'schema' => json_encode($request->scheme),
            'is_active' => $request->status,
            'currency' => $request->currency,
        ]);

        return [
            'error' => false,
            'message' => 'Data has been created.',
        ];
    }

    public function update($request, $id) : array
    {
        $data = $this->model()::findOrFail($id);
        $serviceName = $request->service_name;

        if (
            $data->blueprint != $serviceName
        ) {
            /*
            * Check double data
            */
            $count = $this->checkDoubleData($serviceName);

            if ($count > 0) {
                return [
                    'error' => true,
                    'message' => $serviceName.' service have been already exists.'
                ];
            }
        }

        $data->update([
            'name' => str_replace('_', ' ', strtoupper($serviceName)),
            'blueprint' => strtolower($serviceName),
            'schema' => json_encode($request->scheme),
            'is_active' => $request->status,
            'currency' => $request->currency
        ]);

        return [
            'error' => false,
            'message' => 'Data has been updated.',
        ];
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
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    private function checkDoubleData($serviceName)
    {
        $count = $this->model()::where(
            'blueprint',
            strtolower($serviceName)
        )->count();

        return $count;
    }
}
