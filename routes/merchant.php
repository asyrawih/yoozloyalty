<?php

use Illuminate\Support\Facades\Route;

$merchantRoute = [
    'prefix' => 'merchant',  
    'middleware' => [
        'auth:api',
        'role:3'
    ],
    'namespace' => 'Merchant',
];

Route::group($merchantRoute, function () {
    Route::group(['namespace' => 'Setting'], function () {
        /*
        * SMS Template
        */
        Route::get('sms-template','SmsTemplateController@index')->name('sms-template.index');
        Route::post('sms-template','SmsTemplateController@store')->name('sms-template.store');

        /*
        * Points Expiry
        */
        Route::get('points-expiry','PointsExpiryController@index')->name('points-expiry.index');
        Route::post('points-expiry','PointsExpiryController@update')->name('points-expiry.update');
    }); 
    /*
    * Send Broadcast Notification
    */
    Route::post('send-broadcast-notication','SendBroadcastNotificationController@send')->name('merchant.send-broadcast-notication');
    /*
    * Globat state
    */
    Route::get('get-dropdown-customer','CustomerController@getDropdownCustomer')->name('merchant.get-dropdown-customer');      
});