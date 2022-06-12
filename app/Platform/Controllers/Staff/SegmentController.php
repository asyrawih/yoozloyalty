<?php

namespace Platform\Controllers\Staff;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Staff;
use App\Http\Controllers;
use Platform\Controllers\Core;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Staff Segments
 *
 * Endpoints for staff related to segments
 * @package Platform\Controllers\Staff
 */
class SegmentController extends Controller {

  /*
   |--------------------------------------------------------------------------
   | Segment related functions
   |--------------------------------------------------------------------------
   */

  /**
   * Get campaign segments
   * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
   * 
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getSegments() {
      $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();
      $segments = $campaign->business->segments->pluck('name', 'id');

      return response()->json($segments, 200);
  }
}
