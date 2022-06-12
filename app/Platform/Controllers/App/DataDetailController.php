<?php


namespace Platform\Controllers\App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DataDetailController
{
    /**
     * When a model has detail setting enabled and clicked this will handle what is shown inside that detail dialog.
     *
     * @param Request $request
     */
    public function getModelDetail(Request $request){
        $model = $request->model;
        $uuid = $request->uuid;

        if (! class_exists($model)) {
            return response()->json(['status' => 'error', 'msg' => 'Class does not exist'], 422);
        }
        $extraSelectColumns = $model::getExtraSelectColumns()[auth()->user()->role];
        $extraQueryColumns = $model::getExtraQueryColumns()[auth()->user()->role];

        $select = [];
        $query = $model::select(array_merge($select, $extraSelectColumns, $extraQueryColumns));

        $data = $model::withoutGlobalScopes()->whereUuid($uuid)->first();
        $data = $data->getDetailData();

        if($data){
            return response()->json(['success' => true, 'data' => $data], 200);
        }

        return response()->json(['success' => false, 'message' => "No data found"], 400);
    }
}
