<?php

namespace App\Http\Controllers\Admin\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Admin\BankAccountTypeRepository;

class BankAccountTypeController extends Controller
{
    private BankAccountTypeRepository $bankAccountType;

    public function __construct(BankAccountTypeRepository $bankAccountType)
    {
        $this->bankAccountType = $bankAccountType;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->bankAccountType->all(['*'], 'name');

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'data' => $data,
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->bankAccountType->create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Bank account type has been created.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bankAccountType = $this->bankAccountType->read($id);

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'data' => $bankAccountType,
        ]);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->bankAccountType->update($id, $validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Bank account type has been updated.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->bankAccountType->delete($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Bank account type has been deleted.'
        ]);
    }

    public function getActive()
    {
        $data = $this->bankAccountType->getActiveTypes();

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'data' => $data,
        ]);
    }
}
