<?php

namespace App\Traits;

use App\{
    Models\NotificationServices,
};
use App\Jobs\{
    PieSocketNotificationJob,
    PusherNotificationJob,
    OnesignalNotificationJob
};

trait BroadcastNotificationTrait
{

    public function sendNotif(
        $title,
        $content,
        $users,
        $type
    ) {

        $broadcastServices = NotificationServices::where('is_active', 1)
            ->get();

        /*
        * Check if have not any broadcast service enabled.
        */
        if (!$broadcastServices) {
            return [
                'error' => true,
                'message' => 'Have not any broadcast service enabled.'
            ];
        }

        foreach ($broadcastServices as $broadcastService) {
            $schema = json_decode($broadcastService->schema);
    
            switch ($broadcastService->blueprint) {
    
                case 'pusher':
                    dispatch(
                        new PusherNotificationJob(
                            $title,
                            $content,
                            $schema,
                            $users,
                            $type
                        )
                    )->onQueue('default');
                    break;

                case 'pie_socket':
                    dispatch(
                        new PieSocketNotificationJob(
                            $title,
                            $content,
                            $schema,
                            $users,
                            $type
                        )
                    )->onQueue('default');
                    break;

                case 'onesignal':
                    dispatch(
                        new OnesignalNotificationJob(
                            $title,
                            $content,
                            $schema,
                            $users,
                            $type
                        )
                    )->onQueue('default');
                    break;
            }
        }

        return [
            'error' => false,
            'message' => 'Sending message...'
        ];
    }
}
