<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AllowOnWorkingHours
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') || $request->isMethod('POST') || $request->isMethod('PUT')) {
            $uuid = $request->input('uuid', NULL);

            if ($request->has('campaign')) {
                $uuid = $request->campaign;
            }

            $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid($uuid)->first();

            if ($campaign) {
                $hasBusinesses = auth('staff')->user()->businesses->contains($campaign->business_id);

                if ($hasBusinesses && \App\Repositories\Staff\StoreRepository::isStoreOperational($campaign->business_id)) {
                    return $next($request);
                }
            }

            // if ($request->has('campaign')){
            //     $uuid =
            // } else if($request->has('uuid')) {
            //     $uuid = $request->uuid;
            // } else {
            //     return response()->json([
            //         'error' => 'store_not_open',
            //         'msg' => 'Campaign does not exists.'
            //     ], 401);
            // }
        }

        return response()->json([
            'error' => 'store_not_open',
            'msg' => 'Campaign does not exists.'
        ], 401);

        // else if ($request->isMethod('get')) {
        //     if ($request->has('campaign')){
        //         $uuid = $request->query('campaign');
        //     } else if ($request->has('uuid')) {
        //         $uuid = $request->query('uuid');
        //     } else {
        //         return response()->json([
        //             'error' => 'store_not_open',
        //             'msg' => 'Campaign does not exists.'
        //         ], 401);
        //     }
        // } else {
        //     return response()->json([
        //         'error' => 'store_not_open',
        //         'msg' => 'Method not allowed.'
        //     ], 401);
        // }

        // $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid($uuid)->firstOrFail();

        // $hasPermission = auth('staff')->user()->businesses->contains($campaign->business_id);

        // if ($hasPermission && !\App\Repositories\Staff\StoreRepository::isStoreOperational($campaign->business_id) ) {
        //     return response()->json([
        //         'error' => 'store_not_open'
        //     ], 401);
        // }

        // return $next($request);
    }
}
