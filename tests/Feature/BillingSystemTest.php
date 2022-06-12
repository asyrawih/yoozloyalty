<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Platform\Models\Plan;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class BillingSystemTest extends TestCase
{
    private function admin()
    {
        return User::query()->find(1);
    }

    private function merchant()
    {
        $user = User::query()->where('email', 'unit-test@random.com')->first();

        if (! $user) {
            $user = new User;

            $user->account_id = 1;
            $user->role = 3;
            $user->name = 'Unit Test';
            $user->email = 'unit-test@random.com';
            $user->password = bcrypt('welcome123');
            $user->language = 'en';
            $user->locale = 'en';
            $user->currency_code = 'TTD';
            $user->timezone = 'Asia/Makassar';
            $user->email_verified_at = now();
            $user->phone_personal = '8101010101010';
            $user->country_code = '+62';
            $user->created_by = 1;
            $user->save();
        }

        return $user;
    }

    private function merchantToken()
    {
        return JWTAuth::fromUser($this->merchant());
    }

    private function adminToken()
    {
        return JWTAuth::fromUser($this->admin());
    }

    private function requestHeaders()
    {
        return [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => "Bearer {$this->merchantToken()}"
        ];
    }

    private function requestAdminHeaders()
    {
        return [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => "Bearer {$this->adminToken()}"
        ];
    }

    private function requestUri($patterns)
    {
        return env('APP_URL'). "/api/user/plan-billings/{$patterns}";
    }

    private function requestAdminUri($patterns)
    {
        return env('APP_URL'). "/api/admin/plan-orders/{$patterns}";
    }

    private function createPlanTest(string $currency_code = 'TTD'): Plan
    {
        $plan = Plan::query()
            ->where('name', 'Plan Test')
            ->first();

        if (! $plan) {
            $plan = new Plan;

            $plan->name = 'Plan Test';
            $plan->role = 3;
            $plan->currency_code = $currency_code;
            $plan->price = 1000;
            $plan->limitations = [
                'customers' => 1,
                'campaigns' => 1,
                'rewards' => 1,
                'businesses' => 1,
                'staff' => 1,
                'segments' => 1,
            ];
            $plan->active = 1;
            $plan->save();
        }

        return $plan;
    }

    private function createBankTest(): Bank
    {
        $bank = Bank::query()
            ->where('is_active', 1)
            ->first();

        if (! $bank) {
            $bank = new Bank;

            $bank->bank_name = 'Bank Test';
            $bank->branch_name = 'Branch Bank Test';
            $bank->branch_code = '000';
            $bank->account_name = 'Unit Test';
            $bank->account_number = '0000000000';
            $bank->is_active = 1;
            $bank->save();
        }

        return $bank;
    }

    public function test_request_initialize()
    {
        $response = $this->json(
            'GET',
            $this->requestUri('initialize'),
            [],
            $this->requestHeaders($this->merchantToken())
        );

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('payment_methods')
                ->has('cheques')
                ->has('savings')
            );
    }

    public function test_request_plans()
    {
        $response = $this->json(
            'GET',
            $this->requestUri('plans'),
            [],
            $this->requestHeaders($this->merchantToken())
        );

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->first(fn ($json) => $json
                    ->has('id')
                    ->has('name')
                    ->has('price')
                    ->has('currency')
                    ->has('price_formatted')
                    ->has('interval')
                    ->has('customers')
                    ->has('campaigns')
                    ->has('rewards')
                    ->has('businesses')
                    ->has('staff')
                    ->has('segments')
                    ->has('upgradeable')
                )
            );
    }

    public function test_request_stat()
    {
        $response = $this->json(
            'GET',
            $this->requestUri('stat'),
            [],
            $this->requestHeaders($this->merchantToken())
        );

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('active_plan')
                ->has('expired')
                ->has('subscription_expired_at')
                ->has('status')
                ->has('order_plan')
                ->has('invoice')
                ->has('bank')
            );
    }

    // Cheque

    public function test_scenario_checkout_cancel_order_payment_method_cheque()
    {
        $planTest = $this->createPlanTest();
        $bankTest = $this->createBankTest();
        $amount = $planTest->price / 100;

        $response_checkout = $this->json(
            'POST',
            $this->requestUri('checkout'),
            [
                'plan_id' => $planTest->id,
                'payment_method' => 'cheque',
                'bank_id' => $bankTest->id,
                'amount' => $amount,
                'currency' => $planTest->currency_code,
                'action' => 'buy'
            ],
            $this->requestHeaders()
        );

        $response_checkout->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', null)
                ->etc()
            );

        $merchant = $this->merchant();

        $response_cancel = $this->json(
            'POST',
            $this->requestUri("{$merchant->remote_customer_id}/cancel"),
            [],
            $this->requestHeaders()
        );

        $response_cancel->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Order successfully cancelled.')
                ->etc()
            );
    }

    public function test_scenario_checkout_confirm_for_rejected_order_payment_method_cheque()
    {
        $planTest = $this->createPlanTest();
        $bankTest = $this->createBankTest();
        $amount = $planTest->price / 100;

        $response_checkout = $this->json(
            'POST',
            $this->requestUri('checkout'),
            [
                'plan_id' => $planTest->id,
                'payment_method' => 'cheque',
                'bank_id' => $bankTest->id,
                'amount' => $amount,
                'currency' => $planTest->currency_code,
                'action' => 'buy'
            ],
            $this->requestHeaders()
        );

        $response_checkout->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', null)
                ->etc()
            );

        $merchant = $this->merchant();

        $response_confirm = $this->json(
            'POST',
            $this->requestUri('confirm'),
            [
                'order_id' => $merchant->remote_customer_id,
                'payment_method' => 'cheque',
                'amount' => $amount,
                'merchant_bank_name' => 'Bank Test',
                'merchant_identifier' => '123456789',
                'amount_paid' => $amount,
                'paid_at' => now(),
            ],
            $this->requestHeaders()
        );

        $response_confirm->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Payment confirmation has been sent successfully.')
                ->etc()
            );
    }

    public function test_scenario_rejected_order_payment_method_cheque()
    {
        $merchant = $this->merchant();

        $response_rejected = $this->json(
            'POST',
            $this->requestAdminUri("{$merchant->remote_customer_id}/rejected"),
            [],
            $this->requestAdminHeaders()
        );

        $response_rejected->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Successfully rejected subscription plan.')
                ->etc()
            );
    }

    public function test_scenario_checkout_confirm_for_approved_order_payment_method_cheque()
    {
        $planTest = $this->createPlanTest();
        $bankTest = $this->createBankTest();
        $amount = $planTest->price / 100;

        $response_checkout = $this->json(
            'POST',
            $this->requestUri('checkout'),
            [
                'plan_id' => $planTest->id,
                'payment_method' => 'cheque',
                'bank_id' => $bankTest->id,
                'amount' => $amount,
                'currency' => $planTest->currency_code,
                'action' => 'buy'
            ],
            $this->requestHeaders()
        );

        $response_checkout->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', null)
                ->etc()
            );

        $merchant = $this->merchant();

        $response_confirm = $this->json(
            'POST',
            $this->requestUri('confirm'),
            [
                'order_id' => $merchant->remote_customer_id,
                'payment_method' => 'cheque',
                'amount' => $amount,
                'merchant_bank_name' => 'Bank Test',
                'merchant_identifier' => '123456789',
                'amount_paid' => $amount,
                'paid_at' => now(),
            ],
            $this->requestHeaders()
        );

        $response_confirm->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Payment confirmation has been sent successfully.')
                ->etc()
            );
    }

    public function test_scenario_approved_order_payment_method_cheque()
    {
        $merchant = $this->merchant();

        $response_approved = $this->json(
            'POST',
            $this->requestAdminUri("{$merchant->remote_customer_id}/approved"),
            [],
            $this->requestAdminHeaders()
        );

        $response_approved->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Successfully approved subscription plan.')
                ->etc()
            );
    }

    // Bank Transfer

    public function test_scenario_checkout_cancel_order_payment_method_bank_transfer()
    {
        $planTest = $this->createPlanTest();
        $bankTest = $this->createBankTest();
        $amount = $planTest->price / 100;

        $response_checkout = $this->json(
            'POST',
            $this->requestUri('checkout'),
            [
                'plan_id' => $planTest->id,
                'payment_method' => 'bank_transfer',
                'bank_id' => $bankTest->id,
                'amount' => $amount,
                'currency' => $planTest->currency_code,
                'action' => 'buy'
            ],
            $this->requestHeaders()
        );

        $response_checkout->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', null)
                ->etc()
            );

        $merchant = $this->merchant();

        $response_cancel = $this->json(
            'POST',
            $this->requestUri("{$merchant->remote_customer_id}/cancel"),
            [],
            $this->requestHeaders()
        );

        $response_cancel->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Order successfully cancelled.')
                ->etc()
            );
    }

    public function test_scenario_checkout_confirm_for_rejected_order_payment_method_bank_transfer()
    {
        $planTest = $this->createPlanTest();
        $bankTest = $this->createBankTest();
        $amount = $planTest->price / 100;

        $response_checkout = $this->json(
            'POST',
            $this->requestUri('checkout'),
            [
                'plan_id' => $planTest->id,
                'payment_method' => 'bank_transfer',
                'bank_id' => $bankTest->id,
                'amount' => $amount,
                'currency' => $planTest->currency_code,
                'action' => 'buy'
            ],
            $this->requestHeaders()
        );

        $response_checkout->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', null)
                ->etc()
            );

        $merchant = $this->merchant();

        $response_confirm = $this->json(
            'POST',
            $this->requestUri('confirm'),
            [
                'order_id' => $merchant->remote_customer_id,
                'payment_method' => 'bank_transfer',
                'amount' => $amount,
                'merchant_bank_name' => 'Bank Test',
                'merchant_identifier' => '123456789',
                'amount_paid' => $amount,
                'paid_at' => now(),
            ],
            $this->requestHeaders()
        );

        $response_confirm->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Payment confirmation has been sent successfully.')
                ->etc()
            );
    }

    public function test_scenario_rejected_order_payment_method_bank_transfer()
    {
        $merchant = $this->merchant();

        $response_rejected = $this->json(
            'POST',
            $this->requestAdminUri("{$merchant->remote_customer_id}/rejected"),
            [],
            $this->requestAdminHeaders()
        );

        $response_rejected->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Successfully rejected subscription plan.')
                ->etc()
            );
    }

    public function test_scenario_checkout_confirm_for_approved_order_payment_method_bank_transfer()
    {
        $planTest = $this->createPlanTest();
        $bankTest = $this->createBankTest();
        $amount = $planTest->price / 100;

        $response_checkout = $this->json(
            'POST',
            $this->requestUri('checkout'),
            [
                'plan_id' => $planTest->id,
                'payment_method' => 'bank_transfer',
                'bank_id' => $bankTest->id,
                'amount' => $amount,
                'currency' => $planTest->currency_code,
                'action' => 'buy'
            ],
            $this->requestHeaders()
        );

        $response_checkout->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', null)
                ->etc()
            );

        $merchant = $this->merchant();

        $response_confirm = $this->json(
            'POST',
            $this->requestUri('confirm'),
            [
                'order_id' => $merchant->remote_customer_id,
                'payment_method' => 'bank_transfer',
                'amount' => $amount,
                'merchant_bank_name' => 'Bank Test',
                'merchant_identifier' => '123456789',
                'amount_paid' => $amount,
                'paid_at' => now(),
            ],
            $this->requestHeaders()
        );

        $response_confirm->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Payment confirmation has been sent successfully.')
                ->etc()
            );
    }

    public function test_scenario_approved_order_payment_method_bank_transfer()
    {
        $merchant = $this->merchant();

        $response_approved = $this->json(
            'POST',
            $this->requestAdminUri("{$merchant->remote_customer_id}/approved"),
            [],
            $this->requestAdminHeaders()
        );

        $response_approved->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Successfully approved subscription plan.')
                ->etc()
            );
    }

    // Lynx

    public function test_scenario_checkout_cancel_order_payment_method_bank_lynx()
    {
        $planTest = $this->createPlanTest();
        $amount = $planTest->price / 100;

        $response_checkout = $this->json(
            'POST',
            $this->requestUri('checkout'),
            [
                'plan_id' => $planTest->id,
                'payment_method' => 'lynx',
                'bank_id' => null,
                'amount' => $amount,
                'currency' => $planTest->currency_code,
                'action' => 'buy'
            ],
            $this->requestHeaders()
        );

        $response_checkout->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', null)
                ->etc()
            );

        $merchant = $this->merchant();

        $response_cancel = $this->json(
            'POST',
            $this->requestUri("{$merchant->remote_customer_id}/cancel"),
            [],
            $this->requestHeaders()
        );

        $response_cancel->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Order successfully cancelled.')
                ->etc()
            );
    }

    public function test_scenario_checkout_confirm_rejected_order_payment_method_bank_lynx()
    {
        $planTest = $this->createPlanTest();
        $amount = $planTest->price / 100;

        $response_checkout = $this->json(
            'POST',
            $this->requestUri('checkout'),
            [
                'plan_id' => $planTest->id,
                'payment_method' => 'lynx',
                'bank_id' => null,
                'amount' => $amount,
                'currency' => $planTest->currency_code,
                'action' => 'buy'
            ],
            $this->requestHeaders()
        );

        $response_checkout->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', null)
                ->etc()
            );

        $merchant = $this->merchant();

        $response_confirm = $this->json(
            'POST',
            $this->requestUri('confirm'),
            [
                'order_id' => $merchant->remote_customer_id,
                'payment_method' => 'lynx',
                'amount' => $amount,
                'merchant_bank_name' => null,
                'merchant_identifier' => '123456789',
                'amount_paid' => $amount,
                'paid_at' => now(),
            ],
            $this->requestHeaders()
        );

        $response_confirm->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Payment confirmation has been sent successfully.')
                ->etc()
            );
    }

    public function test_scenario_rejected_order_payment_method_lynx()
    {
        $merchant = $this->merchant();

        $response_rejected = $this->json(
            'POST',
            $this->requestAdminUri("{$merchant->remote_customer_id}/rejected"),
            [],
            $this->requestAdminHeaders()
        );

        $response_rejected->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Successfully rejected subscription plan.')
                ->etc()
            );
    }

    public function test_scenario_checkout_confirm_for_approved_order_payment_method_lynx()
    {
        $planTest = $this->createPlanTest();
        $amount = $planTest->price / 100;

        $response_checkout = $this->json(
            'POST',
            $this->requestUri('checkout'),
            [
                'plan_id' => $planTest->id,
                'payment_method' => 'lynx',
                'bank_id' => null,
                'amount' => $amount,
                'currency' => $planTest->currency_code,
                'action' => 'buy'
            ],
            $this->requestHeaders()
        );

        $response_checkout->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', null)
                ->etc()
            );

        $merchant = $this->merchant();

        $response_confirm = $this->json(
            'POST',
            $this->requestUri('confirm'),
            [
                'order_id' => $merchant->remote_customer_id,
                'payment_method' => 'lynx',
                'amount' => $amount,
                'merchant_bank_name' => null,
                'merchant_identifier' => '123456789',
                'amount_paid' => $amount,
                'paid_at' => now(),
            ],
            $this->requestHeaders()
        );

        $response_confirm->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Payment confirmation has been sent successfully.')
                ->etc()
            );
    }

    public function test_scenario_approved_order_payment_method_lynx()
    {
        $merchant = $this->merchant();

        $response_approved = $this->json(
            'POST',
            $this->requestAdminUri("{$merchant->remote_customer_id}/approved"),
            [],
            [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
                'Authorization' => "Bearer {$this->adminToken()}"
            ]
        );

        $response_approved->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Successfully approved subscription plan.')
                ->etc()
            );
    }

    // Yooz PG
    // public function test_scenario_checkout_failed_order_payment_method_yooz_pg()
    // {
    //     $planTest = $this->createPlanTest();
    //     $amount = $planTest->price / 100;

    //     $response_checkout = $this->json(
    //         'POST',
    //         $this->requestUri('checkout'),
    //         [
    //             'plan_id' => $planTest->id,
    //             'payment_method' => 'yooz_pg',
    //             'bank_id' => null,
    //             'amount' => $amount,
    //             'currency' => $planTest->currency_code,
    //             'action' => 'buy'
    //         ],
    //         $this->requestHeaders($this->merchantToken())
    //     );

    //     $response_checkout->assertOk()
    //         ->assertJson(fn (AssertableJson $json) => $json
    //             ->where('status', 'success')
    //             ->where('message', null)
    //             ->etc()
    //         );

    //     $merchant = $this->merchant();

    //     $response_success = $this->json(
    //         'POST',
    //         env('APP_URL') . "/api/yoozpg/callback/failed/{$merchant->remote_customer_id}",
    //         [],
    //         [
    //             'Accept' => 'application/json',
    //             'X-Requested-With' => 'XMLHttpRequest',
    //         ]
    //     );

    //     $response_success->assertRedirect(env('APP_URL') . '/go#/billing');
    // }

    // public function test_scenario_checkout_success_order_payment_method_yooz_pg()
    // {
    //     $planTest = $this->createPlanTest();
    //     $amount = $planTest->price / 100;

    //     $response_checkout = $this->json(
    //         'POST',
    //         $this->requestUri('checkout'),
    //         [
    //             'plan_id' => $planTest->id,
    //             'payment_method' => 'yooz_pg',
    //             'bank_id' => null,
    //             'amount' => $amount,
    //             'currency' => $planTest->currency_code,
    //             'action' => 'buy'
    //         ],
    //         $this->requestHeaders($this->merchantToken())
    //     );

    //     $response_checkout->assertOk()
    //         ->assertJson(fn (AssertableJson $json) => $json
    //             ->where('status', 'success')
    //             ->where('message', null)
    //             ->etc()
    //         );

    //     $merchant = $this->merchant();

    //     $response_success = $this->json(
    //         'POST',
    //         env('APP_URL') . "/api/yoozpg/callback/success/{$merchant->remote_customer_id}",
    //         [],
    //         [
    //             'Accept' => 'application/json',
    //             'X-Requested-With' => 'XMLHttpRequest',
    //         ]
    //     );

    //     $response_success->assertRedirect(env('APP_URL') . '/go#/billing');
    // }
}
