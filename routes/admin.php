<?php

use Illuminate\Support\Facades\Route;

$adminRoute = [
    'prefix' => 'admin',
    'middleware' => [
        'auth:api',
        'is_admin'
    ],
    'namespace' => 'Admin',
];

Route::group($adminRoute, function () {
    Route::group(['namespace' => 'Setting'], function () {
        /*
        * SMS Settings
        */
        Route::get('sms/test-smpp','SmsSettingController@smpp')->name('sms.test-smpp');
        Route::get('sms/{id}/delete','SmsSettingController@delete')->name('sms.delete');
        Route::match(['post','get'],'sms/test','SmsSettingController@test')->name('sms.test');
        Route::get('sms/{name}/schema','SmsSettingController@getApiScheme')->name('sms.schema');
        Route::resource('sms','SmsSettingController');

        /*
        * SMS Template
        */
        Route::get('sms-template','SmsTemplateController@index')->name('sms-template.index');
        Route::post('sms-template','SmsTemplateController@store')->name('sms-template.store');

        /*
        * Push Notification Settings
        */
        Route::get('push-notication/test-smpp','NotificaionServiceController@smpp')->name('push-notication.test-smpp');
        Route::get('push-notication/{id}/delete','NotificaionServiceController@delete')->name('push-notication.delete');
        Route::match(['post','get'],'push-notication/test','NotificaionServiceController@test')->name('push-notication.test');
        Route::get('push-notication/{name}/schema','NotificaionServiceController@getApiScheme')->name('push-notication.schema');
        Route::resource('push-notication','NotificaionServiceController');

        /*
        * Payment Method Setting
        */
        Route::get('payment-method/{name}/schema','PaymentMethodController@getApiScheme')->name('payment-method.schema');
        Route::resource("payment-method", 'PaymentMethodController');
    });
    /*
    * Send Broadcast Notification
    */
    Route::post('send-broadcast-notication','SendBroadcastNotificationController@send')->name('admin.send-broadcast-notication');
    /*
    * Globat state
    */
    Route::get('get-dropdown-merchant','MerchantController@getDropdownMerchant')->name('admin.get-dropdown-merchant');
});
