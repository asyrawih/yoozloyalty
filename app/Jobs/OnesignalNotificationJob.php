<?php

namespace App\Jobs;

use App\Notifications\PushNotif;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class OnesignalNotificationJob implements ShouldQueue
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
    $title = $this->title;
    $content = $this->content;

    $curl = curl_init();

    $users = $this->users->map(function ($user) {
      return $user->uuid;
    });

    $post_fields = [
      "app_id" => $this->service->app_id,
      "include_external_user_ids" => $users,
      "subtitle" => [
        "en" => $title
      ],
      "contents" => [
        "en" => $content
      ]
    ];

    curl_setopt_array($curl, [
      CURLOPT_URL => $this->service->publish_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_POSTFIELDS => json_encode($post_fields),
      CURLOPT_HTTPHEADER => [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $this->service->api_key
      ],
    ]);

    $response = curl_exec($curl);
  }
}
