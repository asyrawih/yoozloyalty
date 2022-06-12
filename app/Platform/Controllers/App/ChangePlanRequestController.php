<?php

namespace Platform\Controllers\App;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Platform\Models\PlanChangeRequest;

class ChangePlanRequestController extends \App\Http\Controllers\Controller
{
    public function submitPlanChange(Request $request){
        $plan_id = $request->input('plan_id');
        $user = auth()->user();

        $planChange = PlanChangeRequest::create([
            'created_by' => $user->id,
            'previous_plan_id' => $user->plan_id,
            'new_plan_id' => $plan_id,
        ]);

        if($planChange){
            return response()->json(['status' => 'success', 'msg' =>
                __('app.plan_'.$planChange->type.'_request_message')
            ]);
        }

        return response()->json(['status' => 'failed', 'msg' => 'Error'], 400);
    }

    public function cancelPlanChange(Request $request){
        $plan_id = $request->input('plan_id');
        $user = auth()->user();

        $planChange = $user->plan_change_request()->first();
        $planChange->status = PlanChangeRequest::CANCELLED_STATUS;
        $planChange->save();

        if($planChange){
            return response()->json(['status' => 'success', 'msg' =>
                __('app.plan_'.$planChange->type.'_cancel_request_message')
            ]);
        }

        return response()->json(['status' => 'failed', 'msg' => 'Error'], 400);
    }

    public function adminPlanChangeApproval(Request $request)
    {
        $uuid = $request->uuid;
        $action = $request->action;

        $user = User::whereUuid($uuid)->first();

        if(!$user){
            return response()->json(['status' => 'failed', 'msg' => "User not found"], 200);
        }

        $status = $action === 'approve' ? PlanChangeRequest::APPROVED_STATUS : PlanChangeRequest::REJECTED_STATUS;

        $planChange = $user->plan_change_request()->first();
        $planChange->approved_by = auth()->user()->id;
        $planChange->status = $status;
        $planChange->save();
        if ($status === PlanChangeRequest::APPROVED_STATUS) {
            if ($user->expires->isPast()) {
                $user->expires = Carbon::now()->addMonth();
            } else {
                $user->expires = $user->expires->addMonth();
            }
            $user->plan_id = $planChange->newPlan()->first()->id;
            $user->save();
        }

        if($planChange){
            return response()->json(['status' => 'success', 'msg' =>
                __('app.plan_'.$planChange->type.'_approval_message', ['name' => $user->name, 'status' => $status])
            ]);
        }

        return response()->json(['status' => 'failed', 'msg' => 'Error'], 400);
    }
}
