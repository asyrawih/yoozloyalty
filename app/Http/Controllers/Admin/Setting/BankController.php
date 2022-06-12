<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Repositories\BankRepository;
use Exception;
use Illuminate\Http\Request;

class BankController extends Controller
{
    private BankRepository $bankRepository;

    public function __construct(BankRepository $bankRepository)
    {
        $this->bankRepository = $bankRepository;
    }

    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $page = $request->input('page', 1);
        $sortBy = $request->input('sortBy', []);
        $sortDesc = $request->input('sortDesc', []);
        $search = $request->input('search', null);

        try {
            return $this->bankRepository->datatables(compact(
                'itemsPerPage',
                'page',
                'sortBy',
                'sortDesc',
                'search'
            ));
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function show(int $id)
    {
        try {
            return response()->json($this->bankRepository->find($id));
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        return $this->bankRepository->store($request->all());
    }

    public function update(Request $request, int $id)
    {
        return $this->bankRepository->modify($request->all(), $id);
    }

    public function destroy(int $id)
    {
        return $this->bankRepository->destroy($id);
    }
}
