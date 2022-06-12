<?php

namespace App\Jobs;

use App\Mail\TemplateMail;
use Throwable;
use App\Models\SmtpService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Swift_Mailer;
use Swift_SmtpTransport;

class ProcessMerchantSendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email;

    private int|NULL $service_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, int|NULL $service_id)
    {
        $this->email = $email;

        $this->service_id = $service_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = $this->email;

        $defaultSwiftMailer = Mail::getSwiftMailer();

        $swiftMailer = $defaultSwiftMailer;

        $campaignSmtpService = SmtpService::query()
            ->where('id', $this->service_id)
            ->where('is_active', 1)
            ->first();

        if ($campaignSmtpService) {
            $transport = new Swift_SmtpTransport(
                $campaignSmtpService->mail_host,
                $campaignSmtpService->mail_port,
                $campaignSmtpService->mail_encryption,
            );

            $transport->setUsername($campaignSmtpService->mail_username);
            $transport->setPassword($campaignSmtpService->mail_password);

            $swiftMailer = new Swift_Mailer($transport);

            $email->from_name = $campaignSmtpService->mail_from_name;
            $email->from_email = $campaignSmtpService->mail_from_address;
        }

        Mail::setSwiftMailer($swiftMailer);

        Mail::send(new TemplateMail($email));

        Mail::setSwiftMailer($defaultSwiftMailer);
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
        echo $exception->getMessage();
    }
}
