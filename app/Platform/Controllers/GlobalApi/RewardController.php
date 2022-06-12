<?php
namespace Platform\Controllers\GlobalApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Platform\Models\Reward;

/**
 * @group Global
 *
 * Endpoint for retrieving Reward list
 */
class RewardController extends Controller {

    /**
     * Get all reward list     
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){                   
        $campaign = Reward::all();

        return response()->json([
            'data' => $campaign
        ], 200);
    }
}
