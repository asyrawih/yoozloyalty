<?php

namespace App\Traits;

use App\{
    Models\SmsService
};
use App\Jobs\{
    BmobileSmppGatewayJob,
    DigicelSmppGatewayJob,
    TwilioSmsApiJob
};

trait SmsApiTrait
{
    public function sendSms(
        $isdCode,
        $phoneNumber,
        $message
    ): void {

        $smsServices = SmsService::where('is_active', 1)
            ->where('country_isd_code', $isdCode)
            ->get();

        if ($smsServices->count() > 0) {
            foreach ($smsServices as $smsService) {

                $schema = json_decode($smsService->schema);

                switch ($smsService->blueprint) {

                    case 'twilio':
                        dispatch(
                            new TwilioSmsApiJob(
                                $phoneNumber,
                                $message,
                                $schema
                            )
                        )->onQueue(config('queue.works.sms'));
                        break;

                    case 'digicel_smpp_gateway':
                        dispatch(
                            new DigicelSmppGatewayJob(
                                $phoneNumber,
                                $message,
                                $schema
                            )
                        )->onQueue(config('queue.works.sms'));
                        break;

                    case 'bmobile_smpp_gateway':
                        dispatch(
                            new BmobileSmppGatewayJob(
                                $phoneNumber,
                                $message,
                                $schema
                            )
                        )->onQueue(config('queue.works.sms'));
                        break;
                }
            }
        }
    }
}
