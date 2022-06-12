<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Str;

class CreateAdminTest extends TestCase
{
    private function admin()
    {
        return User::query()->find(1);
    }

    private function adminToken()
    {
        return JWTAuth::fromUser($this->admin());
    }

    private function requestUri($patterns = null)
    {
        return env('APP_URL') . "/api/admin/admin-users/{$patterns}";
    }

    private function requestHeaders()
    {
        return [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => 'Bearer '.$this->adminToken()
        ];
    }

    public function test_request_list_of_admin()
    {
        $this->json(
            'GET',
            $this->requestUri(),
            [],
            $this->requestHeaders()
        )->assertOk();
    }

    public function test_request_create_master_admin()
    {
        DB::table('users')->where('email', 'masteradmintest@yoozloyalty.com')->delete();

        $response = $this->json(
            'POST',
            $this->requestUri(),
            [
                'role' => 1,
                'name' => 'Master Admin Test',
                'email' => "masteradmintest@yoozloyalty.com",
                'password' => 'password'
            ],
            $this->requestHeaders()
        );

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Admin has been created.')
            );
    }

    public function test_request_show_master_admin()
    {
        $user = User::query()->where('email', 'masteradmintest@yoozloyalty.com')->first();

        $this->json(
            'GET',
            $this->requestUri($user->id),
            [],
            $this->requestHeaders()
        )
            ->assertOk();
    }

    public function test_request_update_master_admin()
    {
        $user = User::query()->where('email', 'masteradmintest@yoozloyalty.com')->first();

        $this->json(
            'PUT',
            $this->requestUri($user->id),
            [
                'role' => 1,
                'name' => 'Master Admin Test 1',
                'email' => 'masteradmintest@yoozloyalty.com',
                'password' => 'password'
            ],
            $this->requestHeaders()
        )
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Data admin has been updated.')
            );
    }

    public function test_request_delete_master_admin()
    {
        $user = User::query()->where('email', 'masteradmintest@yoozloyalty.com')->first();

        $this->json(
            'DELETE',
            $this->requestUri($user->id),
            [],
            $this->requestHeaders()
        )
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Data admin has been deleted.')
            );
    }

    public function test_request_create_normal_admin()
    {
        DB::table('users')->where('email', 'normaladmintest@yoozloyalty.com')->delete();

        $response = $this->json(
            'POST',
            $this->requestUri(),
            [
                'role' => 2,
                'name' => 'Normal Admin Test',
                'email' => 'normaladmintest@yoozloyalty.com',
                'password' => 'password'
            ],
            $this->requestHeaders()
        );

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Admin has been created.')
            );
    }

    public function test_request_show_normal_admin()
    {
        $user = User::query()->where('email', 'normaladmintest@yoozloyalty.com')->first();

        $this->json(
            'GET',
            $this->requestUri($user->id),
            [],
            $this->requestHeaders()
        )
            ->assertOk();
    }

    public function test_request_update_normal_admin()
    {
        $user = User::query()->where('email', 'normaladmintest@yoozloyalty.com')->first();

        $this->json(
            'PUT',
            $this->requestUri($user->id),
            [
                'role' => 1,
                'name' => 'Normal Admin Test 1',
                'email' => 'normaladmintest@yoozloyalty.com',
                'password' => 'password'
            ],
            $this->requestHeaders()
        )
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Data admin has been updated.')
            );
    }

    public function test_request_delete_normal_admin()
    {
        $user = User::query()->where('email', 'normaladmintest@yoozloyalty.com')->first();

        $this->json(
            'DELETE',
            $this->requestUri($user->id),
            [],
            $this->requestHeaders()
        )
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('status', 'success')
                ->where('message', 'Data admin has been deleted.')
            );
    }
}
