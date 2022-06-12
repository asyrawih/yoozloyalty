<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use Platform\Models\History;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\NotifPusherRepositories;
use App\Repositories\Campaign\CampaignRepository;
use App\Repositories\Staff\CreditRequestRepository;

class CreditRequestController extends Controller
{
    private CampaignRepository $campaign;
    private CreditRequestRepository $creditRequest;
    private EmailTemplateRepository $emailTemplate;
    private NotifPusherRepositories $notifPusher;

    public function __construct(
        CampaignRepository $campaign,
        CreditRequestRepository $creditRequest,
        EmailTemplateRepository $emailTemplate,
        NotifPusherRepositories $notifPusher
    ) {
        $this->campaign = $campaign;
        $this->creditRequest = $creditRequest;
        $this->emailTemplate = $emailTemplate;
        $this->notifPusher = $notifPusher;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaign = $this->campaign->readUuid(request('uuid'));

        $page = request('page', 1);
        $perPage = request('perPage', 10);
        $filters = request('filters', []);

        $datatables = $this->creditRequest->datatables($campaign->id, $page, $perPage, $filters);

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'data' => $datatables['data'],
            'meta' => $datatables['meta']
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $campaign = $this->campaign->readUuid(request('uuid'));

        $creditRequest = $this->creditRequest->read($id, ['*'], ['campaign', 'customer']);

        if ($request->status === 'approved') {
            $staff = auth('staff')->user();

            $history = new History;
            $history->customer_id = $creditRequest->customer_id;
            $history->campaign_id = $campaign->id;
            $history->staff_id = $staff->id;
            $history->staff_name = $staff->name;
            $history->staff_email = $staff->email;
            $history->points = $creditRequest->points;
            $history->points_expired_date = Carbon::now()->addDays($campaign->user->points_expiry->points_expiry ?? config('system.default_points_expiry'));
            $history->bill_number = $creditRequest->receipt_number;
            $history->bill_amount = $creditRequest->receipt_amount;
            $history->event = 'Credit Request';
            $history->created_by = $campaign->created_by;
            $history->save();

            $this->emailTemplate->customerCreditPoint(
                $creditRequest->campaign,
                $creditRequest->customer,
                $creditRequest->points
            );


            if ($this->notifPusher->isAvailable()) {
                $this->notifPusher->pointCredited(
                    $creditRequest->customer,
                    $creditRequest->points,
                    'Credit Request'
                );
            }
        }

        $creditRequest->status = $request->status;
        $creditRequest->updated_by = auth('staff')->id();

        $creditRequest->save();

        return response()->json([
            'status' => 'success',
            'message' => NULL
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
        //
    }
}
