<?php

namespace Tests\Feature;

use App\ {
    User,
    Staff
};
use Platform\Models\ {
    Plan,
    Business,
    Reward,
    Campaign,
    Segment
};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PlanLimitationsTest extends TestCase
{

    private function plan()
    {
        $plan = Plan::first();
        $plan->update([
            'limitations' => [
                'customers' => 1,
                'campaigns' => 1,
                'rewards' => 1,
                'businesses' => 1,
                'staff' => 1,
                'segments' => 1
            ]
        ]);

        return $plan;
    }

    private function user()
    {
        $user = User::query()->where('email', 'unit-test@random.com')->first();

        if (!$user) {
            $user = new User;
        }

        $user->account_id = 1;
        $user->role = 3;
        $user->plan_id = $this->plan()->id;
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
        $user->save();

        $user->created_by = $user->id;
        $user->save();

        return $user;
    }

    private function createReward($user)
    {

        $reward = Reward::where('created_by', $user->id)->first();

        if (! $reward) {
            $reward = new Reward;

            $reward->account_id = 1;
            $reward->created_by = $user->id;
            $reward->title = 'Unit Test';
            $reward->description = 'Unit Test';
            $reward->points_cost = 2;
            $reward->reward_value = 2;
            $reward->active_from = now();
            $reward->expires_at = now();
            $reward->created_at = now();
            $reward->save();
        }

        return $reward;
    }

    private function createBusiness($user)
    {
        $business = Business::where('created_by', $user->id)->first();

        if(!$business) {
            $business = new \Platform\Models\Business;

            $business->account_id = 1;
            $business->name = 'Derana Mart';
            $business->email = $user->email;
            $business->industry_id = mt_rand(1, 10);
            $business->website = '';
            $business->phone = '';
            $business->street1 = '';
            $business->city = '';
            $business->state = '';
            $business->postal_code = '';
            $business->created_by = $user->id;

            $business->content = [
                'href1' => 'https://example.com',
                'text1' => 'https://example.com'
            ];

            $business->social = [
                'text' => 'Connect with us!',
                /*'medium' => 'https://example.com',*/
                'twitter' => 'https://example.com',
                /*'youtube' => 'https://example.com',*/
                'facebook' => 'https://example.com',
                'linkedin' => 'https://example.com',
                /*'telegram' => 'https://example.com',*/
                'whatsapp' => 'https://example.com',
                'instagram' => 'https://example.com'
            ];

            $business->save();
        }

        return $business;
    }

    private function createCampaign($user)
    {
        $campaign = Campaign::where('created_by', $user->id)->first();

        if (! $campaign) {
            $campaign = new Campaign;

            $campaign->account_id = 1;
            $campaign->business_id = $this->createBusiness($user)->id;
            $campaign->name = 'Unit Test Campaign';
            $campaign->signup_bonus_points = 1;
            $campaign->content = [];
            $campaign->settings = [];
            $campaign->created_by = $user->id;
            $campaign->save();
        }

        return $campaign;
    }

    private function createStaff($user)
    {
        $staff = Staff::where('created_by', $user->id)->first();

        if (! $staff) {
            $staff = new Staff;
            $staff->account_id = 1;
            $staff->role = 1;
            $staff->name = 'Unit Test User';
            $staff->email = 'unittest@gmail.com';
            $staff->password = bcrypt('welcome123');
            $staff->created_by = $user->id;
            $staff->save();
        }

        return $staff;
    }

    private function createSegment($user)
    {
        $segment = Segment::where('created_by', $user->id)->first();

        if(! $segment) {
            $segment = new Segment;

            $segment->account_id = 1;
            $segment->name = 'Segment UNIT Test';
            $segment->created_by = $user->id;
            $segment->save();
        }

        return $segment;
    }

    private function token()
    {
        return JWTAuth::fromUser($this->user());
    }

    /**
     * Test create a campaign plan limitations
     * @test
     * @return void
     */
    public function website()
    {

        $this->createBusiness($this->user());

        $this->json(
            'POST',
            env('APP_URL').'/api/app/data-form/save', [
                'locale' => 'en-US',
                'uuid' => null,
                'model' => 'Platform\Models\Business'
            ],[
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
                'Authorization' => 'Bearer '.$this->token()
            ]
        )->assertStatus(422)->assertJson([
            'status' => 'error',
            'msg' => 'You have limit excedeed to create a website.'
        ]);
    }

    /**
     * Test create a campaign plan limitations
     * @test
     * @return void
     */
    public function campaign()
    {

        $this->createCampaign($this->user());

        $this->json(
            'POST',
            env('APP_URL').'/api/app/data-form/save', [
                'locale' => 'en-US',
                'uuid' => null,
                'model' => 'Platform\Models\Campaign'
            ],[
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
                'Authorization' => 'Bearer '.$this->token()
            ]
        )->assertStatus(422)->assertJson([
            'status' => 'error',
            'msg' => 'You have limit excedeed to create a campaign.'
        ]);
    }

    /**
     * Test create a staff plan limitations
     * @test
     * @return void
     */
    public function staff()
    {

        $this->createStaff($this->user());

        $this->json(
            'POST',
            env('APP_URL').'/api/app/data-form/save', [
                'locale' => 'en-US',
                'uuid' => null,
                'model' => 'App\Staff'
            ],[
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
                'Authorization' => 'Bearer '.$this->token()
            ]
        )->assertStatus(422)->assertJson([
            'status' => 'error',
            'msg' => 'You have limit excedeed to create a staff.'
        ]);
    }

    /**
     * Test create a reward plan limitations
     * @test
     * @return void
     */
    public function reward()
    {

        $this->createReward($this->user());

        $this->json(
            'POST',
            env('APP_URL').'/api/app/data-form/save', [
                'locale' => 'en-US',
                'uuid' => null,
                'model' => 'Platform\Models\Reward',
                'title' => 'New Reward 4',
                'points_cost' => 2,
                'reward_value' => 2,
                'active_from' => 1,
                'expires_at' => date('Y-m-d H:i:s')
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
                'Authorization' => 'Bearer '.$this->token()
            ]
        )->assertStatus(422)->assertJson([
            'status' => 'error',
            'msg' => 'You have limit excedeed to create a reward.'
        ]);
    }

    /**
     * Test create a segment plan limitations
     * @test
     * @return void
     */
    public function segment()
    {
        $this->createSegment($this->user());

        $this->json(
            'POST',
            env('APP_URL').'/api/app/data-form/save', [
                'locale' => 'en-US',
                'uuid' => null,
                'name' => 'Unit Test Segment',
                'model' => 'Platform\Models\Segment'
            ],[
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
                'Authorization' => 'Bearer '.$this->token()
            ]
        )->assertStatus(422)->assertJson([
            'status' => 'error',
            'msg' => 'You have limit excedeed to create a segment.'
        ]);
    }
}
