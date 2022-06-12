<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    Merchant\YoozController,
    Merchant\PaytmController,
    Merchant\PayuController,
    Merchant\InstamojoController,
    Merchant\PaypalController,
    Admin\Setting\PlanController,
    Admin\Setting\PlanOrderController,
    LegalController,
    Merchant\Setting\PlanChangeRequestHistoryController,
    Merchant\ExportReportController
};
use App\Http\Controllers\Admin\Setting\BankAccountTypeController;
use App\Http\Controllers\Merchant\Setting\SmtpServiceController;
use App\Http\Controllers\Merchant\CalculatorController;
use App\Http\Controllers\Merchant\CreditRequestController as MerchantCreditRequestController;
use App\Http\Controllers\Merchant\TransactionHistoryController;
use App\Http\Controllers\Staff\CreditRequestController;
use Platform\Controllers\Reward\DateRangeFormController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| https://laravel.com/docs/5.8/controllers#resource-controllers
|
*/

// Public routes
Route::get('i18n/campaign_translations', '\Platform\Controllers\Core\Content@getAvailableCampaignTranslations');
Route::get('i18n/campaign/{lang}', '\Platform\Controllers\Core\Content@getCampaignTranslations');

Route::get('analytics/earning/exports', [ExportReportController::class, 'credits']);

Route::group(['prefix' => 'localization'], function () {
    Route::get('locales', '\Platform\Controllers\Core\Localization@getLocales');
    Route::get('timezones', '\Platform\Controllers\Core\Localization@getTimezones');
    Route::get('currencies', '\Platform\Controllers\Core\Localization@getCurrencies');
    Route::get('country', '\Platform\Controllers\Core\Localization@getCountry');
    Route::get('full-country', '\Platform\Controllers\Core\Localization@getFullCountry');
    Route::get('country-code', '\Platform\Controllers\Core\Localization@getCountryCode');
    Route::get('city', '\Platform\Controllers\Core\Localization@getCity');
});

Route::get('/user/legals', [LegalController::class, 'index']);

Route::group(['prefix' => 'get'], function () {
    Route::get('terms', '\Platform\Controllers\Campaign\CampaignController@getTerms');
    Route::get('site/terms', '\Platform\Controllers\Website\SiteController@getTerms');
});

// Payment webhooks
Route::post('webhooks/paddle', '\Platform\Controllers\App\PaddleController@postWebhook');
Route::post('webhooks/2checkout/ipn', '\Platform\Controllers\App\TwoCheckoutController@postIpn');
Route::post('webhooks/stripe', '\Platform\Controllers\App\StripeController@postWebhook');

/**
 * Yooz Payment Geteway Response
 */
Route::post('yoozpg/callback/success', [YoozController::class, 'success']);
Route::post('yoozpg/callback/failed', [YoozController::class, 'failed']);

/**
 * PAYTM Geteway Response
 */
Route::post('paytm/callback', [PaytmController::class, 'callback']);

/**
 * PAYU Geteway Response
 */
Route::post('payu/callback/{status}', [PayuController::class, 'callback']);

/**
 * instamojo Geteway Response
 */
Route::get('instamojo/callback', [InstamojoController::class, 'callback']);
Route::post('instamojo/webhook', [InstamojoController::class, 'webhook']);

/**
 * Paypal Gateway Response
 */
Route::get('paypal/callback/{status}', [PaypalController::class, 'callback']);

// Route::get('/customers/legals', [LegalController::class, 'index']);

/**
 * Get active notification service provider
 */
Route::get('notification-service/active', '\Platform\Controllers\App\NotificationServiceController@getActiveService');

// Secured app routes
Route::group(['middleware' => 'auth:api'], function () {

    // Admin related routes
    Route::group(['prefix' => 'admin'], function () {
        Route::group(['middleware' => 'is_admin'], function () {
            /**
             * Admin User
             */
            Route::resource('admin-users', \Admin\UserController::class)
                ->only(['index', 'store', 'show', 'update', 'destroy']);

            Route::get('stats', '\Platform\Controllers\App\AdminController@getStats');
            Route::get('notifUnread', '\Platform\Controllers\App\NotifController@getNotifUnread');
            Route::post('maskAsRead', '\Platform\Controllers\App\NotifController@postMasAsRead');
            Route::post('verify-domain', '\Platform\Controllers\App\AdminController@postVerifyDomain');
            Route::post('verify-dns', '\Platform\Controllers\App\AdminController@postVerifyDns');
            Route::post('business', '\Platform\Controllers\App\AdminController@postSaveBusiness');
            Route::post('payment', '\Platform\Controllers\App\AdminController@postSavePayment');
            Route::get('store', '\Platform\Controllers\App\AdminController@getStore');
            Route::post('store', '\Platform\Controllers\App\AdminController@postUpdateStore');
            Route::get('trial', '\Platform\Controllers\App\AdminController@getTrial');
            Route::post('trial', '\Platform\Controllers\App\AdminController@postUpdateTrial');
            Route::get('trial', '\Platform\Controllers\App\AdminController@getTrial');
            Route::get('logo', '\Platform\Controllers\App\AdminController@getLogo');
            Route::post('logo', '\Platform\Controllers\App\AdminController@postUpdateLogo');

            Route::get('legal', '\Platform\Controllers\App\AdminController@getLegal')->withoutMiddleware(['auth:api', 'is_admin']);

            Route::post('legal', '\Platform\Controllers\App\AdminController@postUpdateLegal')->withoutMiddleware(['is_admin']);
            Route::get('email', '\Platform\Controllers\App\AdminController@getEmail');
            Route::post('email', '\Platform\Controllers\App\AdminController@postUpdateEmail');
            Route::post('testing-email', '\Platform\Controllers\App\AdminController@postTestEmail');
            Route::get('email-template', '\Platform\Controllers\App\AdminController@getEmailTemplate');
            Route::post('email-template', '\Platform\Controllers\App\AdminController@postUpdateEmailTemplate');
            Route::get('payment', '\Platform\Controllers\App\AdminController@getPayment');
            Route::post('payment', '\Platform\Controllers\App\AdminController@postUpdatePayment');
            Route::get('push-notif', '\Platform\Controllers\App\AdminController@getPushNotif');
            Route::post('push-notif', '\Platform\Controllers\App\AdminController@postUpdatePushNotif');
            Route::get('notif-template', '\Platform\Controllers\App\NotifTemplateController@index');
            Route::post('notif-template', '\Platform\Controllers\App\NotifTemplateController@update');
            Route::post('domain-guide', '\Platform\Controllers\App\AdminController@postDomainGuide');

            /**
             * Plan
             */
            Route::get('plans/initialize', [PlanController::class, 'initialize']);
            Route::post('plans/massdelete', [PlanController::class, 'massdelete']);
            Route::resource('plans', \Admin\Setting\PlanController::class)
                ->only(['index', 'store', 'show', 'update', 'destroy']);

            /**
             * Bank
             */
            Route::apiResource('bank-accounts', \Admin\Setting\BankAccountController::class);
            Route::get('bank-account-types/active', [BankAccountTypeController::class, 'getActive']);
            Route::apiResource('bank-account-types', \Admin\Setting\BankAccountTypeController::class);

            /**
             * Plan Order
             */
            Route::get('plan-orders', [PlanOrderController::class, 'index']);
            Route::post('plan-orders/{order}/approved', [PlanOrderController::class, 'approved']);
            Route::post('plan-orders/{order}/rejected', [PlanOrderController::class, 'rejected']);

            /**
             * Staff Roles
             */
            Route::resource('staff-roles', \Admin\Setting\StaffRolesController::class)
                ->only(['index', 'store', 'show', 'update', 'destroy']);
        });
        Route::get('domain-guide', '\Platform\Controllers\App\AdminController@getDomainGuide');
    });

    // User related routes
    Route::group(['prefix' => 'user',  'middleware' => 'role:3'], function () {
        Route::get('stats', '\Platform\Controllers\App\UserController@getStats');
        Route::post('analytics/earning', '\Platform\Controllers\App\AnalyticsController@getUserEarningAnalytics');
        Route::get('analytics/earning/exports', [ExportReportController::class, 'credits']);
        Route::post('analytics/spending', '\Platform\Controllers\App\AnalyticsController@getUserSpendingAnalytics');
        Route::get('analytics/spending/exports', [ExportReportController::class, 'redemptions']);

        // Calculator
        Route::post('calculator/credit-points', [CalculatorController::class, 'credit_points']);

        // Credit Request
        // Route::get('credit-requests', \Merchant\CreditRequest::class);
        Route::get('credit-requests/campaigns', [MerchantCreditRequestController::class, 'campaigns']);
        Route::post('credit-requests/import', [MerchantCreditRequestController::class, 'import']);
        Route::post('credit-requests/bulk-actions', [MerchantCreditRequestController::class, 'bulkActions']);
        Route::apiResource('credit-requests', \Merchant\CreditRequestController::class);

        // Transaction History
        Route::get('transaction-histories/campaigns', [TransactionHistoryController::class, 'campaigns']);
        Route::post('transaction-histories/import', [TransactionHistoryController::class, 'import']);
        Route::apiResource('transaction-histories', \Merchant\TransactionHistoryController::class);

        // Notif
        Route::get('notifUnread', '\Platform\Controllers\App\NotifController@getNotifUnread');
        Route::post('maskAsRead', '\Platform\Controllers\App\NotifController@postMasAsRead');

        // User plans
        Route::get('plans', '\Platform\Controllers\App\UserController@getPlans');

        // Stripe
        Route::post('stripe/token', '\Platform\Controllers\App\StripeController@postToken');
        Route::post('stripe/cancel', '\Platform\Controllers\App\StripeController@postCancelSubscription');

        // Paypal
        Route::post('paypal/subcription', '\Platform\Controllers\App\PaypalController@subcription');
        Route::post('paypal/cancel', '\Platform\Controllers\App\PaypalController@postCancelSubscription');

        // Yooz PG
        Route::post('yooz/checkout', '\Platform\Controllers\App\YoozController@checkout');
        Route::post('yooz/unsubcription', '\Platform\Controllers\App\YoozController@unsubcription');

        // Plan Billing
        Route::get('plan-billings/initialize', [\App\Http\Controllers\Merchant\PlanBillingController::class, 'initialize']);
        Route::get('plan-billings/plans', [\App\Http\Controllers\Merchant\PlanBillingController::class, 'plans']);
        Route::get('plan-billings/stat', [\App\Http\Controllers\Merchant\PlanBillingController::class, 'stat']);
        Route::post('plan-billings/checkout', [\App\Http\Controllers\Merchant\PlanBillingController::class, 'checkout']);
        Route::post('plan-billings/confirm', [\App\Http\Controllers\Merchant\PlanBillingController::class, 'confirm']);
        Route::post('plan-billings/{order}/cancel', [\App\Http\Controllers\Merchant\PlanBillingController::class, 'cancel']);

        // Setting
        Route::group(['prefix' => 'setting'], function () {

            Route::get('legals', [LegalController::class, 'index']);
            Route::post('legals', [LegalController::class, 'store']);

            // Email template
            Route::group(['prefix' => 'email-template'], function () {
                Route::get('/', '\Platform\Controllers\Campaign\EmailTemplateController@index');
                Route::post('/', '\Platform\Controllers\Campaign\EmailTemplateController@update');
            });

            // Redeem transaction setting
            Route::group(['prefix' => 'redeem-transaction'], function () {
                Route::get('/', '\Platform\Controllers\Campaign\RedeemTransaction@index');
                Route::post('/', '\Platform\Controllers\Campaign\RedeemTransaction@update');
            });

            // Plan change history setting
            Route::get('plan-orders', [PlanChangeRequestHistoryController::class, 'index']);
            // '\Platform\Controllers\Merchant\Setting\PlanOrderController@index');

            // Smtp Services
            Route::post('smtp-services/initialize', [SmtpServiceController::class, 'initialize']);
            Route::get('smtp-services/websites/{id}', [SmtpServiceController::class, 'websites']);
            Route::apiResource('smtp-services', '\App\Http\Controllers\Merchant\Setting\SmtpServiceController');
        });

        // Reward Date Range
        Route::get('reward/campaigns', [DateRangeFormController::class, 'getCampaigns']);
        Route::post('reward/save-date-range', [DateRangeFormController::class, 'postSaveDateRange']);
    });
});

include_once 'admin.php';
include_once 'merchant.php';

// App authorization routes
Route::prefix('auth')->group(function () {
    Route::post('register', '\Platform\Controllers\App\AuthController@register');
    Route::post('login', '\Platform\Controllers\App\AuthController@login');
    Route::post('impersonate', '\Platform\Controllers\App\AuthController@impersonate');
    Route::get('refresh', '\Platform\Controllers\App\AuthController@refresh');

    // Verification email route
    Route::get('email/resend', '\Platform\Controllers\App\VerificationController@resend')->name('verification.send');
    Route::get('email/verify/{id}/{hash}', '\Platform\Controllers\App\VerificationController@verify')->name('verification.verify');

    // Password route
    Route::post('password/reset', '\Platform\Controllers\App\AuthController@passwordReset');
    Route::post('password/reset/validate-token', '\Platform\Controllers\App\AuthController@passwordResetValidateToken');
    Route::post('password/update', '\Platform\Controllers\App\AuthController@passwordUpdate');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('user', '\Platform\Controllers\App\AuthController@user'); // Get user details
        Route::post('logout', '\Platform\Controllers\App\AuthController@logout');
        Route::post('profile', '\Platform\Controllers\App\AuthController@postUpdateProfile');
    });
});

// Secured app routes
Route::group(['prefix' => 'app', 'middleware' => ['auth:api', 'verified']], function () {

    // DataTable
    Route::get('data-table', '\Platform\Controllers\App\DataTableController@getDataList');
    Route::post('data-table/delete', '\Platform\Controllers\App\DataTableController@postDeleteRecords');
    Route::post('data-table/restore', '\Platform\Controllers\App\DataTableController@postRestoreRecords');
    Route::get('data-table/export', '\Platform\Controllers\App\DataTableController@getExport');

    // DataForm
    Route::get('data-form', '\Platform\Controllers\App\DataFormController@getDataForm');
    Route::post('data-form/relation', '\Platform\Controllers\App\DataFormController@postGetRelation');
    Route::post('data-form/save', '\Platform\Controllers\App\DataFormController@postSaveRecord');
    Route::post('data-form/import', '\Platform\Controllers\App\DataFormController@processImport');

    // DataDetail
    Route::get('data-detail', '\Platform\Controllers\App\DataDetailController@getModelDetail');

    // Customer change plan route
    Route::get('customer/plan/cancel', '\Platform\Controllers\App\ChangePlanRequestController@cancelPlanChange');
    Route::get('customer/plan/submit', '\Platform\Controllers\App\ChangePlanRequestController@submitPlanChange');

    // Credit request update status
    Route::get('credit-request', '\Platform\Controllers\App\CreditRequestController@index');
    Route::post('credit-request', '\Platform\Controllers\App\CreditRequestController@update');

    // Admin change plan request approval route
    Route::post('plan', '\Platform\Controllers\App\ChangePlanRequestController@adminPlanChangeApproval')->name('planApprovalRoute');
});

// Campaigns
Route::get('/website/{slug}', '\Platform\Controllers\Campaign\CampaignApiController@getCampaign');
Route::get('/website/{slug}/logged', '\Platform\Controllers\Campaign\CampaignApiController@getCampaignLogged')->middleware('auth:customer');
Route::post('/website/renew', '\Platform\Controllers\Campaign\CampaignApiController@renew')->middleware('auth:customer');

Route::get('campaign/legals', [LegalController::class, 'index']);
Route::get('campaign/stores', '\Platform\Controllers\Campaign\StoreController@index');
Route::get('campaign/rewards', '\Platform\Controllers\Campaign\RewardController@getActiveRewards');

Route::prefix('campaign/auth')->group(function () {
    Route::post('register', '\Platform\Controllers\Campaign\AuthController@register');
    Route::post('login', '\Platform\Controllers\Campaign\AuthController@login');
    Route::post('loginApp', '\Platform\Controllers\Campaign\AuthController@loginApp');
    Route::get('refresh', '\Platform\Controllers\Campaign\AuthController@refresh');
    Route::post('password/reset', '\Platform\Controllers\Campaign\AuthController@passwordReset');
    Route::post('password/reset/validate-token', '\Platform\Controllers\Campaign\AuthController@passwordResetValidateToken');
    Route::post('password/update', '\Platform\Controllers\Campaign\AuthController@passwordUpdate');
    Route::post('password/update-encrypted', '\Platform\Controllers\Campaign\AuthController@updateEncryptedpassword');

    // Verification email route
    Route::get('email/resend', '\Platform\Controllers\Campaign\VerificationController@resend')->name('customer.verification.send');
    Route::get('email/verify/{id?}/{hash?}', '\Platform\Controllers\Campaign\VerificationController@verify')->name('customer.verification.verify');

    Route::group(['middleware' => 'auth:customer'], function () {
        Route::get('user', '\Platform\Controllers\Campaign\AuthController@user'); // Get user details
        Route::get('website', '\Platform\Controllers\Campaign\AuthController@userWebsite'); // Get user details
        Route::post('logout', '\Platform\Controllers\Campaign\AuthController@logout');
        Route::post('profile', '\Platform\Controllers\Campaign\AuthController@postUpdateProfile');
        Route::get('otp', '\Platform\Controllers\Campaign\AuthController@requestOtp');
        Route::post('otp', '\Platform\Controllers\Campaign\AuthController@verifyOtp');
    });
});

// Secured customer routes
Route::group(['prefix' => 'campaign', 'middleware' => 'auth:customer'], function () {

    // Get points total for customer
    Route::get('points', '\Platform\Controllers\Campaign\PointController@getCustomerPoints');

    /*
    * Get credit request by customer id form auth
    */
    Route::get('credit-requests', '\Platform\Controllers\Campaign\CreditRequestController@getAllCreditRequestsByCustomerId');

    // Get history for customer
    Route::get('history-web', '\Platform\Controllers\Campaign\HistoryController@getHistory');
    Route::get('history', '\Platform\Controllers\Campaign\HistoryController@getHistoryMobile');
    Route::get('history/multiple', '\Platform\Controllers\Campaign\HistoryController@getHistoryMultiple');

    // -------------------------------------------------------------------
    // Routes related to earning points
    // -------------------------------------------------------------------

    // Generate token for use with QR / links
    Route::post('get-claim-points-token', '\Platform\Controllers\Campaign\PointController@postGetClaimToken');

    // Customer verifies code generated by merchant
    Route::post('earn/verify-customer-code', '\Platform\Controllers\Campaign\PointController@postVerifyCustomerCode');

    // Routes for merchant interactions on customer's device
    Route::post('earn/verify-merchant-code', '\Platform\Controllers\Campaign\PointController@postVerifyMerchantCode');
    Route::post('earn/process-merchant-entry', '\Platform\Controllers\Campaign\PointController@postProcessMerchantEntry');

    // Credit request
    Route::post('credit-request', '\Platform\Controllers\Campaign\CreditRequestController@store');

    // -------------------------------------------------------------------
    // Routes related to redeeming rewards
    // -------------------------------------------------------------------

    // Generate token for use with QR / links
    Route::post('get-redeem-reward-token', '\Platform\Controllers\Campaign\RewardController@postGetRedeemRewardToken');

    // Routes for merchant interactions on customer's device
    // Route::get('rewards', '\Platform\Controllers\Campaign\RewardController@getActiveRewards');
    Route::post('reward/verify-merchant-code', '\Platform\Controllers\Campaign\RewardController@postVerifyMerchantCode');
    Route::post('reward/process-merchant-entry', '\Platform\Controllers\Campaign\RewardController@postProcessMerchantEntry');

    // Notif
    Route::get('notifUnread', '\Platform\Controllers\Campaign\NotifController@getNotifUnread');
    Route::post('maskAsRead', '\Platform\Controllers\Campaign\NotifController@postMasAsRead');
});

// Staff
Route::prefix('staff/auth')->group(function () {
    Route::post('login', '\Platform\Controllers\Staff\AuthController@login');
    Route::get('refresh', '\Platform\Controllers\Staff\AuthController@refresh');
    Route::post('password/reset', '\Platform\Controllers\Staff\AuthController@passwordReset');
    Route::post('password/reset/validate-token', '\Platform\Controllers\Staff\AuthController@passwordResetValidateToken');
    Route::post('password/update', '\Platform\Controllers\Staff\AuthController@passwordUpdate');

    Route::group(['middleware' => ['auth:staff, workinghours']], function () {
        Route::get('abilities', '\Platform\Controllers\Staff\AuthController@abilities');
        Route::get('user', '\Platform\Controllers\Staff\AuthController@user'); // Get user details
        Route::post('logout', '\Platform\Controllers\Staff\AuthController@logout');
        Route::post('profile', '\Platform\Controllers\Staff\AuthController@postUpdateProfile');
        Route::get('otp', '\Platform\Controllers\Staff\AuthController@requestOtp');
        Route::post('otp', '\Platform\Controllers\Staff\AuthController@verifyOtp');
    });
});

// Secured staff routes
Route::group(['prefix' => 'staff', 'middleware' => ['auth:staff', 'workinghours']], function () {
    // Route for credit request
    // Route::apiResource('credit-request', \Staff\CreditRequestController::class)->only(['index', 'update']);
    Route::get('credit-request', [CreditRequestController::class, 'index']);
    Route::put('credit-request/{id}', [CreditRequestController::class, 'update']);

    // Get history for staff member
    Route::get('history', '\Platform\Controllers\Staff\HistoryController@getHistory');

    // Get campaign segments
    Route::get('segments', '\Platform\Controllers\Staff\SegmentController@getSegments');

    // Get campaign rewards
    Route::get('rewards', '\Platform\Controllers\Staff\RewardController@getRewards');

    // -------------------------------------------------------------------------------------------------------------------------------------------------------
    // Routes related to earning points
    // -------------------------------------------------------------------------------------------------------------------------------------------------------

    // Routes for customer links
    Route::post('points/validate-link-token', '\Platform\Controllers\Staff\PointController@postValidateLinkToken');
    Route::post('points/push/credit', '\Platform\Controllers\Staff\PointController@postPushCreditsByToken');

    // Routes for customer codes
    Route::get('points/customer/active-codes', '\Platform\Controllers\Staff\PointController@getActiveCustomerCodes');
    Route::post('points/customer/generate-code', '\Platform\Controllers\Staff\PointController@postGenerateCustomerCode');

    // Routes for merchant code
    Route::get('points/merchant/active-code', '\Platform\Controllers\Staff\PointController@getActiveMerchantCode');
    Route::post('points/merchant/generate-code', '\Platform\Controllers\Staff\PointController@postGenerateMerchantCode');

    // Route for customer number
    Route::post('points/customer/credit', '\Platform\Controllers\Staff\PointController@postCreditCustomer');

    // -------------------------------------------------------------------------------------------------------------------------------------------------------
    // Routes related to redeeming rewards
    // -------------------------------------------------------------------------------------------------------------------------------------------------------

    // Routes for customer links
    Route::post('rewards/validate-link-token', '\Platform\Controllers\Staff\RewardController@postValidateLinkToken');
    Route::post('rewards/push/redemption', '\Platform\Controllers\Staff\RewardController@postRedeemRewardByToken');

    // Routes for merchant code
    Route::get('rewards/merchant/active-code', '\Platform\Controllers\Staff\RewardController@getActiveMerchantCode');
    Route::post('rewards/merchant/generate-code', '\Platform\Controllers\Staff\RewardController@postGenerateMerchantCode');

    // Route for customer number
    Route::post('rewards/customer/credit', '\Platform\Controllers\Staff\RewardController@postRedeemReward');

    // Route for Customer CRUD
    // Route::get('/customers', '\Platform\Controllers\Staff\CustomerController@getCustomers');
    // Route::get('/customers/campaign', '\Platform\Controllers\Staff\CustomerController@getCampaign');
    // Route::post('/customers/save', '\Platform\Controllers\Staff\CustomerController@postCreateCustomer');

    Route::apiResource('customers', \Staff\CustomerController::class);

    // Route for Customer point details
    Route::get('/customers/point-details', '\Platform\Controllers\Staff\CustomerController@getPointDetails');
});

// Api that can acces with staff and customer
Route::group(['middleware' => 'auth:staff,customer'], function () {

    // Get stores for customer
    // Route::get('/campaign/stores', '\Platform\Controllers\Campaign\StoreController@index');

    // Get idustry
    Route::get('/industry', '\Platform\Controllers\GlobalApi\IndustryController@index')->withoutMiddleware(['auth:staff,customer']);
    Route::get('/industry/{id}', '\Platform\Controllers\GlobalApi\IndustryController@website')->withoutMiddleware(['auth:staff,customer']);
});
