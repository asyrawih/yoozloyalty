<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;

class TwilioSmsApiJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phoneNumber, $message, $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(String $phoneNumber, String $message, Object $service)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->service = $service;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $client = new Client(
            $this->service->account_sid, $this->service->account_auth_token
        );

        $message = $client->messages->create($this->phoneNumber, [
            'from' => $this->service->sender_number,
            'body' => $this->message
        ]);
    }
}
