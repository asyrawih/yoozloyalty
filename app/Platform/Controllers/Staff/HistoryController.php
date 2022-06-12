<?php

namespace Platform\Controllers\Staff;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Staff;
use App\Http\Controllers;
use Platform\Controllers\Core;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Platform\Models\History;

use function PHPSTORM_META\map;

/**
 * @group Staff History
 *
 * Endpoints for staff related to reward redemption history
 * @package Platform\Controllers\Staff
 */
class HistoryController extends Controller {

    /*
     |--------------------------------------------------------------------------
     | History related functions
     |--------------------------------------------------------------------------
     */

    /**
     * Get staff member history.
     * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getHistory()
    {
        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

        $user = Staff::withoutGlobalScopes()->where('id', auth('staff')->id())->firstOrFail();

        $page = request('page', 1);
        $perPage = request('perPage', 10);

        $histories = History::query()
            ->with(['segments', 'customer'])
            ->where('campaign_id', $campaign->id)
            ->where('staff_id', $user->id)
            ->paginate($perPage, ['*'], 'page', $page);

        $items = collect($histories->items())->map(function ($item) use($user) {
            $item->created_at = $item->created_at->timezone($user->getTimezone());

            $segments = $item->segments->map(function ($segment) {
                return collect($segment)->only('name');
            });

            $item->segment_details = $segments;

            $item->customer_details = collect($item->customer)->only('avatar', 'name', 'number', 'points');

            return collect($item)->only('color', 'created_at', 'description', 'icon', 'points', 'reward_title', 'segment_details', 'customer_details', 'staff_name');
        });

        return response()->json([
            'status' => 'success',
            'message' => NULL,
            'data' => $items->toArray(),
            'meta' => [
                'current_page' => $histories->currentPage(),
                'per_page' => $histories->perPage(),
                'total' => $histories->total(),
            ],
        ]);
    }
}
