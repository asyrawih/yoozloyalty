<?php

namespace App\Http\Controllers\Merchant;

use App\Exports\Merchant\CreditHistoryExport;
use App\Exports\Merchant\RewardHistoryExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportReportController extends Controller
{
    public function credits()
    {
        return Excel::raw(new CreditHistoryExport, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function redemptions()
    {
        return Excel::raw(new RewardHistoryExport, \Maatwebsite\Excel\Excel::XLSX);
    }
}
