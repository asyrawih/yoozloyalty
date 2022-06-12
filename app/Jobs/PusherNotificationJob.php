<?php

namespace App\Jobs;

use App\Notifications\PushNotif;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class PusherNotificationJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title, $content, $service, $users, $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        String $title,
        String $content,
        Object $service,
        Object $users,
        String $type
    ) {
        $this->title = $title;
        $this->content = $content;
        $this->service = $service;
        $this->users = $users;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $options = array(
            'cluster' => $this->service->cluster,
            'useTLS' => true
        );

        $pusher = new \Pusher\Pusher(
            $this->service->app_key,
            $this->service->app_secret,
            $this->service->app_id,
            $options
        );

        $title = $this->title;
        $content = $this->content;
        $type = $this->type;

        $this->users->each(function ($user) use ($pusher, $title, $content, $type) {
            $data = [
                'data' => [
                    'title' => $title,
                    'content' => $content,
                ]
            ];

            $user->notify(new PushNotif($data));

            $notifs = $user->unreadNotifications;

            $data = [];

            foreach ($notifs as $notif) {
                array_push($data, [
                    'id' => $notif->id,
                    'created_at' => Carbon::parse($notif->created_at, config('app.timezone'))->setTimezone($user->getTimezone())->diffForHumans(),
                    'data' => $notif->data
                ]);
            }

            $pusher->trigger($user->uuid, $type, json_encode($data));
        });
    }
}
