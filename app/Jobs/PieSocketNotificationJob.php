<?php

namespace App\Jobs;

use App\Notifications\PushNotif;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class PieSocketNotificationJob implements ShouldQueue
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

    $message = [
      'title' => $title,
      'content' => $content,
      'type' => $this->type
    ];

    $curl = curl_init();

    // $type = $this->type;
    $this->users->each(function ($user) use ($curl, $message) {

      $post_fields = [
        "key" => $this->service->api_key,
        "secret" => $this->service->secret_key,
        "channelId" => 1,
        "message" => $message
      ];

      curl_setopt_array($curl, [
        CURLOPT_URL => $this->service->publish_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($post_fields),
        CURLOPT_HTTPHEADER => [
          'Content-Type: application/json'
        ],
      ]);

      $response = curl_exec($curl);

      $data = [
        'data' => [
          'title' => $message['title'],
          'content' => $message['content'],
        ]
      ];

      $user->notify(new PushNotif($data));
    });
  }
}
