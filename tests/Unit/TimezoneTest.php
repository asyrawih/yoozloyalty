<?php

namespace Tests\Unit;

use Platform\Models\Timezone;
use Database\Seeders\TimezoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class TimezoneTest extends TestCase
{
    /**
     * Timezone unit test.
     *
     * @return void
     */
    public function test_can_retrieve_timezone_list()
    {
        $this->seed(TimezoneSeeder::class);
        $timezones = Timezone::get();
        $this->assertTrue(!is_null($timezones));
    }
}
