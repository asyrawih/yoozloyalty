<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotifTemplate;

class NotifTemplateSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        NotifTemplate::truncate();

        NotifTemplate::create([
            'name' => 'admin_merchant_registeration',
            'subject' => 'New merchant register',
            'template' => '{{ name_of_user }} {{ email_of_user }} register at {{ register_time }}'
        ]);

        NotifTemplate::create([
            'name' => 'merchant_customer_registeration',
            'subject' => 'New customer register',
            'template' => '{{ name_of_user }} {{ email_of_user }} register at {{ register_time }}'
        ]);

        NotifTemplate::create([
            'name' => 'merchant_customer_new_credit_request',
            'subject' => 'New credit request',
            'template' => '{{ name_of_user }} {{ email_of_user }} request new credit , amount : {{ request_amount }}'
        ]);

        NotifTemplate::create([
            'name' => 'merchant_customer_new_reward_redeem',
            'subject' => 'New reward redeemed',
            'template' => '{{ name_of_user }} {{ email_of_user }} redeemed a reward : {{ reward_name }}'
        ]);

        NotifTemplate::create([
            'name' => 'customer_point_credited',
            'subject' => 'Congratulations you got {{ point_credited }}',
            'template' => 'you got {{ point_credited }} by {{ event }}'
        ]);

        NotifTemplate::create([
            'name' => 'customer_point_redeemed',
            'subject' => 'Congratulations you redeemed {{ point_redeemed }}',
            'template' => 'you got {{ redeem_for }}'
        ]);

        NotifTemplate::create([
            'name' => 'customer_welcome_message',
            'subject' => 'Welcome {{ name_of_user }}',
            'template' => 'Thank you for register to {{ website_name }}'
        ]);
    }
}
