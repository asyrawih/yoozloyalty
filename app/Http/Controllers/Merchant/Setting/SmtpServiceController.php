<?php

namespace App\Http\Controllers\Merchant\Setting;

use App\Http\Controllers\Controller;
use App\Models\SmtpService;
use Exception;
use Illuminate\Http\Request;
use Platform\Models\Campaign;

class SmtpServiceController extends Controller
{
    public function initialize(Request $request)
    {
        $user = $request->user();

        $hasCustomDomain = Campaign::query()
            ->where(function ($query) {
                $query->whereNotNull('host')
                    ->orWhere('host', '!=', '');
            })
            ->where('created_by', $user->id)
            ->count();

        return response()->json([
            'status' => 'success',
            'hasCustomDomain' => $hasCustomDomain
        ]);
    }

    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $page = $request->input('page', 1);
        $search = $request->input('search', null);

        $services = SmtpService::query()
            ->when(! empty($search), function ($query) use($search) {
                $query->where('smtp_name', 'like', "%{$search}%");
            })
            ->paginate($itemsPerPage, ['id', 'smtp_name', 'is_active'], 'page', $page);

        return response()->json($services);
    }

    public function store(Request $request)
    {
        try {
            $attributes = $request->only([
                'smtp_name',
                'mail_from_name',
                'mail_from_address',
                'mail_driver',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'mail_encryption',
            ]);

            SmtpService::query()->create($attributes);

            return response()->json([
                'status' => 'success',
                'message' => 'SMTP service has been added.'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        $smtpservice = SmtpService::query()->find($id);

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'data' => $smtpservice,
        ]);
    }

    public function update(Request $request, $id)
    {
        $smtpservice = SmtpService::query()->find($id);

        $attributes = $request->only([
            'smtp_name',
            'mail_from_name',
            'mail_from_address',
            'mail_driver',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'is_active',
        ]);

        $smtpservice->update($attributes);

        return response()->json([
            'status' => 'success',
            'message' => 'SMTP service has been updated.',
        ]);
    }

    public function destroy($id)
    {
        $smtpservice = SmtpService::query()->find($id);

        $smtpservice->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'SMTP service has been deleted.',
        ]);
    }

    public function websites($id)
    {
        $smtpservice = SmtpService::query()->with('campaigns:host,smtp_service_id')->find($id);

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'websites' => $smtpservice->campaigns->pluck('host')->join(', ', ' and '),
        ]);
    }
}
