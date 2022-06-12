<?php
namespace App\Repositories;
use App\Notifications\PushNotif;
use App\User;
use App\Customer;
use App\Models\NotifTemplate;
use App\Traits\BroadcastNotificationTrait;
use Carbon\Carbon;
use Exception;
use App\Models\NotificationServices;

class NotifPusherRepositories
{
    use BroadcastNotificationTrait;

    protected $settings;

    function __construct()
    {
        $settings = NotificationServices::where('blueprint', 'pusher')->first();
        if ($settings) {
            $this->settings = json_decode($settings->schema);
        }
    }

    public function isAvailable(): bool
    {
        return !empty($this->settings);
    }

    public function registerMerchant($usersRegister){
        $notifTemplate = NotifTemplate::where('name', 'admin_merchant_registeration')->first();
        $variableTemplate = ['{{ name_of_user }}', '{{ email_of_user }}', '{{ register_time }}'];
        $variableChange = [$usersRegister->name, $usersRegister->email, $usersRegister->created_at];
        $title = str_replace($variableTemplate, $variableChange ,$notifTemplate->subject);
        $content = str_replace($variableTemplate, $variableChange ,$notifTemplate->template);

        $users = User::where('role', 1)->get();

        // $options = array(
        //     'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        //     'useTLS' => true
        //     );

        // $pusher = new \Pusher\Pusher(
        //     config('broadcasting.connections.pusher.key'),
        //     config('broadcasting.connections.pusher.secret'),
        //     config('broadcasting.connections.pusher.app_id'),
        //     $options
        // );
        
        // Get the pusher setting from database
        $options = array(
            'cluster' => $this->settings->cluster,
            'useTLS' => true
            );

        $pusher = new \Pusher\Pusher(
            $this->settings->app_key,
            $this->settings->app_secret,
            $this->settings->app_id,
            $options
        );

        $users->each(function ($user) use ($pusher, $title, $content){
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

        $pusher->trigger($user->uuid, 'adminNotif', json_encode($data));
        });
    }

    public function registerCustomer($usersRegister)
    {
        try {
            $notifTemplate = NotifTemplate::where('name', 'merchant_customer_registeration')->first();

            $variableTemplate = [
                '{{ name_of_user }}',
                '{{ email_of_user }}',
                '{{ register_time }}'
            ];

            $variableChange = [
                $usersRegister->name,
                $usersRegister->email,
                $usersRegister->created_at
            ];

            $title = str_replace($variableTemplate, $variableChange, $notifTemplate->subject);

            $content = str_replace($variableTemplate, $variableChange, $notifTemplate->template);

            $user = User::find($usersRegister->created_by);

            // $options = array(
            //     'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            //     'useTLS' => true
            // );

            // $pusher = new \Pusher\Pusher(
            //     config('broadcasting.connections.pusher.key'),
            //     config('broadcasting.connections.pusher.secret'),
            //     config('broadcasting.connections.pusher.app_id'),
            //     $options
            // );
        
            // Get the pusher setting from database
            $options = array(
                'cluster' => $this->settings->cluster,
                'useTLS' => true
                );

            $pusher = new \Pusher\Pusher(
                $this->settings->app_key,
                $this->settings->app_secret,
                $this->settings->app_id,
                $options
            );

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

            $pusher->trigger($user->uuid, 'adminNotif', json_encode($data));
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function redeemReward($user, $reward){
        $notifTemplate = NotifTemplate::where('name', 'merchant_customer_new_reward_redeem')->first();
        $variableTemplate = ['{{ name_of_user }}', '{{ email_of_user }}', '{{ reward_name }}'];
        $variableChange = [$user->name, $user->email, $reward->title];
        $title = str_replace($variableTemplate, $variableChange ,$notifTemplate->subject);
        $content = str_replace($variableTemplate, $variableChange ,$notifTemplate->template);

        $user = User::find($user->created_by);

        // $options = array(
        //     'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        //     'useTLS' => true
        //     );

        // $pusher = new \Pusher\Pusher(
        //     config('broadcasting.connections.pusher.key'),
        //     config('broadcasting.connections.pusher.secret'),
        //     config('broadcasting.connections.pusher.app_id'),
        //     $options
        // );


        
        // Get the pusher setting from database
        $options = array(
            'cluster' => $this->settings->cluster,
            'useTLS' => true
            );

        $pusher = new \Pusher\Pusher(
            $this->settings->app_key,
            $this->settings->app_secret,
            $this->settings->app_id,
            $options
        );

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

        $pusher->trigger($user->uuid, 'adminNotif', json_encode($data));
    }

    public function creditRequest($user, $requestAmount){
        $notifTemplate = NotifTemplate::query()
            ->where('name', 'merchant_customer_new_credit_request')
            ->first();

        $variableTemplate = [
            '{{ name_of_user }}',
            '{{ email_of_user }}',
            '{{ request_amount }}'
        ];

        $variableChange = [
            $user->name,
            $user->email,
            $requestAmount
        ];

        $title = str_replace($variableTemplate, $variableChange, $notifTemplate->subject);

        $content = str_replace($variableTemplate, $variableChange, $notifTemplate->template);

        $user = User::query()->find($user->created_by);

        // $options = array(
        //     'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        //     'useTLS' => true
        // );

        // $pusher = new \Pusher\Pusher(
        //     config('broadcasting.connections.pusher.key'),
        //     config('broadcasting.connections.pusher.secret'),
        //     config('broadcasting.connections.pusher.app_id'),
        //     $options
        // );

        
        // Get the pusher setting from database
        $options = array(
            'cluster' => $this->settings->cluster,
            'useTLS' => true
            );

        $pusher = new \Pusher\Pusher(
            $this->settings->app_key,
            $this->settings->app_secret,
            $this->settings->app_id,
            $options
        );

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

        $pusher->trigger($user->uuid, 'adminNotif', json_encode($data));
    }

    public function pointCredited($user, $point, $event)
    {
        $notifTemplate = NotifTemplate::where('name', 'customer_point_credited')->first();

        $variableTemplate = [
            '{{ name_of_user }}',
            '{{ email_of_user }}',
            '{{ point_credited }}',
            '{{ event }}'
        ];

        $variableChange = [
            $user->name,
            $user->email,
            $point, $event
        ];

        $title = str_replace($variableTemplate, $variableChange, $notifTemplate->subject);

        $content = str_replace($variableTemplate, $variableChange, $notifTemplate->template);

        $user = Customer::query()->find($user->id);

        // $options = array(
        //     'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        //     'useTLS' => true
        // );

        // $pusher = new \Pusher\Pusher(
        //     config('broadcasting.connections.pusher.key'),
        //     config('broadcasting.connections.pusher.secret'),
        //     config('broadcasting.connections.pusher.app_id'),
        //     $options
        // );

        
        // Get the pusher setting from database
        $options = array(
            'cluster' => $this->settings->cluster,
            'useTLS' => true
            );

        $pusher = new \Pusher\Pusher(
            $this->settings->app_key,
            $this->settings->app_secret,
            $this->settings->app_id,
            $options
        );

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

        $pusher->trigger($user->uuid, 'customerNotif', json_encode($data));
    }

    public function pointRedeemed($user, $point, $reward){
        $notifTemplate = NotifTemplate::where('name', 'customer_point_redeemed')->first();
        $variableTemplate = ['{{ name_of_user }}', '{{ email_of_user }}', '{{ point_redeemed }}', '{{ redeem_for }}'];
        $variableChange = [$user->name, $user->email, $point, $reward];
        $title = str_replace($variableTemplate, $variableChange ,$notifTemplate->subject);
        $content = str_replace($variableTemplate, $variableChange ,$notifTemplate->template);

        $user = Customer::find($user->id);

        // $options = array(
        //     'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        //     'useTLS' => true
        //     );

        // $pusher = new \Pusher\Pusher(
        //     config('broadcasting.connections.pusher.key'),
        //     config('broadcasting.connections.pusher.secret'),
        //     config('broadcasting.connections.pusher.app_id'),
        //     $options
        // );

        
        // Get the pusher setting from database
        $options = array(
            'cluster' => $this->settings->cluster,
            'useTLS' => true
            );

        $pusher = new \Pusher\Pusher(
            $this->settings->app_key,
            $this->settings->app_secret,
            $this->settings->app_id,
            $options
        );

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

        $pusher->trigger($user->uuid, 'customerNotif', json_encode($data));
    }

    public function customerWelcomeMessage($user, $websiteName)
    {
        try {
            $notifTemplate = NotifTemplate::where('name', 'customer_welcome_message')->first();

            $variableTemplate = [
                '{{ name_of_user }}',
                '{{ email_of_user }}',
                '{{ website_name }}'
            ];

            $variableChange = [
                $user->name,
                $user->email,
                $websiteName
            ];

            $title = str_replace($variableTemplate, $variableChange ,$notifTemplate->subject);

            $content = str_replace($variableTemplate, $variableChange ,$notifTemplate->template);

            $user = Customer::find($user->id);

            // $options = array(
            //     'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            //     'useTLS' => true
            // );

            // $pusher = new \Pusher\Pusher(
            //     config('broadcasting.connections.pusher.key'),
            //     config('broadcasting.connections.pusher.secret'),
            //     config('broadcasting.connections.pusher.app_id'),
            //     $options
            // );

        
            // Get the pusher setting from database
            $options = array(
                'cluster' => $this->settings->cluster,
                'useTLS' => true
                );

            $pusher = new \Pusher\Pusher(
                $this->settings->app_key,
                $this->settings->app_secret,
                $this->settings->app_id,
                $options
            );

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

            $pusher->trigger($user->uuid, 'customerNotif', json_encode($data));
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function sendBroadcast($request)
    {
        $title = $request->title;
        $content = $request->message;

        $users = User::query()->whereIn('id', $request->merchants)->get(['id', 'uuid']);

        //send broadcast and get respone
        return $this->sendNotif($title, $content, $users, 'adminNotif');
    }

    public function sendBroadcastCustomer($request)
    {
        $title = $request->title;
        $content = $request->message;

        $users = Customer::query()->whereIn('id', $request->customers)->get(['id', 'uuid']);

        //send broadcast and get respone
        return $this->sendNotif($title, $content, $users, 'customerNotif');
  }
}
