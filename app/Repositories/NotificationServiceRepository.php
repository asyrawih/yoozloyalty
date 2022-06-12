<?php

namespace App\Repositories;

use App\Models\ {
    NotificationServices
};

class NotificationServiceRepository extends BaseRepository
{

    /**
    * NotificationServiceRepository constructor.
    *
    * @param NotificationServices $model
    */
    public function __construct(NotificationServices $model)
    {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return NotificationServices::class;
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
        return storage_path('json/settings/notification/services.json');
    }

    public function serviceBluePrint() : string
    {
        return storage_path('json/settings/notification/schema/{file}.json');
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

        if ($request->status) {
            $this->updateActiveService();
        }

        $this->model()::create([
            'name' => str_replace('_', ' ', strtoupper($serviceName)),
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

        // if ($request->status) {
        //     $this->updateActiveService();
        // }

        $data->update([
            'name' => str_replace('_', ' ', strtoupper($serviceName)),
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
            return $this->showException($e->getMessage());
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

    private function updateActiveService()
    {
        return $this->model()::where('is_active',1)->update([
            'is_active' => 0
        ]);
    }

}
