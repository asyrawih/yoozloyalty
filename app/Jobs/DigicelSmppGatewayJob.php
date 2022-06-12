<?php

namespace App\Jobs;

use App\Libraries\Smpp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DigicelSmppGatewayJob implements ShouldQueue
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
		$client = new Smpp();
		// $client->debug=1;

		// $host,$port,$system_id,$password
		$client->open(
            $this->service->host, 
            $this->service->port,
            $this->service->username,
            $this->service->password
        );

		// $source_addr,$destintation_addr,$short_message,$utf=0,$flash=0
		$client->send_long(
            $this->service->sender_number,
            $this->phoneNumber,
            $this->message
        );

		/* To send unicode 
		$utf = true;
		$message = iconv('Windows-1256','UTF-16BE',$message);
		$client->send_long($src, $dst, $message, $utf);
		*/  

		$client->close();
    }
}
