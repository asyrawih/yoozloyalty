<?php

namespace App\Http\Controllers\Admin\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\BankAccountRepository;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    private BankAccountRepository $bankAccount;

    public function __construct(BankAccountRepository $bankAccount)
    {
        $this->bankAccount = $bankAccount;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = request('page', 1);
        $perPage = request('perPage', 10);
        $filters = request('filters', []);

        $datatables = $this->bankAccount->datatables($page, $perPage, $filters);

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'data' => $datatables['data'],
            'meta' => $datatables['meta'],
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
            'bank_name' => 'required',
            'branch_name' => 'required',
            'branch_code' => 'required',
            'account_name' => 'required',
            'account_type_id' => 'required',
            'account_number' => 'required',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->bankAccount->create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Bank account has been created.',
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
        $bankAccount = $this->bankAccount->read($id);

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'data' => $bankAccount,
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
            'bank_name' => 'required',
            'branch_name' => 'required',
            'branch_code' => 'required',
            'account_name' => 'required',
            'account_type_id' => 'required',
            'account_number' => 'required',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->bankAccount->update($id, $validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Bank account has been updated.',
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
        $this->bankAccount->delete($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Bank account has been deleted.'
        ]);
    }
}
