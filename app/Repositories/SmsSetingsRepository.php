<?php

namespace App\Repositories;

use App\Models\ {
    SmsService
};

class SmsSetingsRepository extends BaseRepository
{

    /**
    * SmsSetingsRepository constructor.
    *
    * @param SmsService $model
    */
    public function __construct(SmsService $model)
    {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SmsService::class;
    }

    public function datatable($request)
    {
        $data = $this->model()::select('*');

        if (isset($request['isd_code'])) {
            $data->where(
                'country_isd_code',
                $request['isd_code']
            );
        }

        if (isset($request['sms_services'])) {
            $data->where(
                'name',
                'like',
                '%'.$request['sms_services'].'%'
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
        return storage_path('json/settings/sms/services.json');
    }

    public function serviceBluePrint() : string
    {
        return storage_path('json/settings/sms/schema/{file}.json');
    }

    public function store($request) : array
    {
        $serviceName = $request->service_name;

        /*
        * Check double data
        */
        $count = $this->checkDoubleData($serviceName,$request->isd_code);

        if ($count > 0) {
            return [
                'error' => true,
                'message' => 'These ISD Code and SMS Service have been already exists.'
            ];
        }

        $this->model()::create([
            'name' => str_replace('_', ' ', strtoupper($serviceName)),
            'country_isd_code' => $request->isd_code,
            'blueprint' => strtolower($serviceName),
            'schema' => json_encode($request->scheme),
            'is_active' => $request->status
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
            $data->country_isd_code != $request->isd_code ||
            $data->blueprint != $serviceName
        ) {
            /*
            * Check double data
            */
            $count = $this->checkDoubleData($serviceName,$request->isd_code);

            if ($count > 0) {
                return [
                    'error' => true,
                    'message' => 'These ISD Code and SMS Service have been already exists.'
                ];
            }
        }

        $data->update([
            'name' => str_replace('_', ' ', strtoupper($serviceName)),
            'country_isd_code' => $request->isd_code,
            'blueprint' => strtolower($serviceName),
            'schema' => json_encode($request->scheme),
            'is_active' => $request->status
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
                'message' => $e->getMessage(),
            ];
        }
    }

    private function checkDoubleData($serviceName, $isd_code)
    {
        $count = $this->model()::where(
            'blueprint',
            strtolower($serviceName)
        )
        ->where(
            'country_isd_code',
            $isd_code
        )->count();

        return $count;
    }

}
