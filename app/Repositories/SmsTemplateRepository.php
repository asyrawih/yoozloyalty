<?php

namespace App\Repositories;

use App\Models\{
    SmsTemplate
};
use App\Traits\ExceptionHandlingTrait;

class SmsTemplateRepository extends BaseRepository
{
    use ExceptionHandlingTrait;
    /**
     * SmsTemplateRepository constructor.
     *
     * @param SmsTemplate $model
     */
    public function __construct(SmsTemplate $model)
    {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SmsTemplate::class;
    }

    public function index($userId)
    {
        try {
            if (auth()->user()->role == 1) {
                $data = $this->adminIndexTemplate($userId);
            } else {
                $data = $this->merchantIndexTemplate($userId);
            }

            return [
                'error' => false,
                'message' => 'Sms Template Setting.',
                'data' => $data,

            ];
        } catch (\Exception $e) {
            return $this->showException($e->getMessage());
        }
    }

    private function adminIndexTemplate($userId)
    {
        $merchant_registration = $this->model()::where('created_by', $userId)->where('name', 'merchant_registration')->first();

        $merchant_payment_confirmation = $this->model()::where('created_by', $userId)->where('name', 'merchant_payment_confirmation')->first();


        $data = [
            'merchant_registration' => [
                'id' => $merchant_registration->id ?? '',
                'title' => $merchant_registration->title ?? '',
                'template' => $merchant_registration->template ?? '',
            ],
            'merchant_payment_confirmation' => [
                'id' => $merchant_payment_confirmation->id ?? '',
                'title' => $merchant_payment_confirmation->title ?? '',
                'template' => $merchant_payment_confirmation->template ?? '',
            ],
        ];

        return $data;
    }

    private function merchantIndexTemplate($userId)
    {
        $customer_registration = $this->model()::where('created_by', $userId)->where('name', 'customer_registration')->first();

        $customer_point_credit = $this->model()::where('created_by', $userId)->where('name', 'customer_point_credit')->first();

        $customer_point_redeem = $this->model()::where('created_by', $userId)->where('name', 'customer_point_redeem')->first();

        $customer_point_successful_redemption = $this->model()::where('created_by', $userId)->where('name', 'customer_point_successful_redemption')->first();

        $customer_birthday = $this->model()::where('created_by', $userId)->where('name', 'customer_birthday')->first();

        $data = [
            'customer_registration' => [
                'id' => $customer_registration->id ?? '',
                'title' => $customer_registration->title ?? '',
                'template' => $customer_registration->template ?? '',
            ],
            'customer_point_credit' => [
                'id' => $customer_point_credit->id ?? '',
                'title' => $customer_point_credit->title ?? '',
                'template' => $customer_point_credit->template ?? '',
            ],
            'customer_point_redeem' => [
                'id' => $customer_point_redeem->id ?? '',
                'title' => $customer_point_redeem->title ?? '',
                'template' => $customer_point_redeem->template ?? '',
            ],
            'customer_point_successful_redemption' => [
                'id' => $customer_point_successful_redemption->id ?? '',
                'title' => $customer_point_successful_redemption->title ?? '',
                'template' => $customer_point_successful_redemption->template ?? '',
            ],
            'customer_birthday' => [
                'id' => $customer_birthday->id ?? '',
                'title' => $customer_birthday->title ?? '',
                'template' => $customer_birthday->template ?? '',
            ],
        ];

        return $data;
    }

    public function store($request, $userId)
    {
        try {
            $merchant_registration = $this->model()::updateOrCreate(
                //check exists name and created by
                [
                    'name' => $request->type,
                    'created_by' => $userId
                ],
                //field to be updated.
                [
                    'name' => $request->type,
                    'template' => $request->template,
                    'created_by' => $userId
                ]
            );


            if ($merchant_registration) {
                return [
                    'error' => false,
                    'message' => 'Data has been updated.'
                ];
            }

            return [
                'error' => true,
                'message' => 'Error update.'
            ];
        } catch (\Exception $e) {
            return $this->showException($e->getMessage());
        }
    }

    /**
     * Email verification template
     *
     * @param string $name_of_user
     * @param string $verification_url
     */
    public function smsMerchantRegistrationTemplate(
        string $name_of_user,
        date $register_time,
        string $email_of_user,
        $userId
    ) {
        $smsTemplate = $this->model()::where('created_by', $userId)
            ->where('name', 'merchant_registration')
            ->first();

        if ($smsTemplate) {
            $variableTemplate = [
                '{{ name_of_user }}',
                '{{ email_of_user }}',
                '{{ register_time }}'
            ];

            $variableChange = [
                $name_of_user,
                $email_of_user,
                $register_time
            ];

            $template = $smsTemplate->transformTemplate($variableTemplate, $variableChange);

            return compact('template');
        }

        return false;
    }
}
