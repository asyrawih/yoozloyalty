<?php

namespace Platform\Controllers\App;

use Illuminate\Http\Request;
use App\Models\NotificationServices;

/**
 * @group PushNotification
 *
 * Endpoint to check user's pushNotification subscription status
 *
 * @package Platform\Controllers\App
 */
class NotificationServiceController extends \App\Http\Controllers\Controller
{

	/*
   |--------------------------------------------------------------------------
   | NotificationService Controller
   |--------------------------------------------------------------------------
   */

	/**
	 * Get active service
	 */
	public function getActiveService()
	{
		$activeNotificationService = NotificationServices::where('is_active',1)->get();

		return $this->apiResponse([
            'error' => false,
            'message' => 'Active Notification Service',
            'data' => $activeNotificationService
        ]);
	}
}
