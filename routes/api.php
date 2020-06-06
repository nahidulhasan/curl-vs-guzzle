<?php

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'v1'/*, 'middleware' => ['log.request']*/], function () {

    Route::post('send-otp', 'API\V1\RegistrationController@sendOTP');

    Route::post('resend-otp', 'API\V1\RegistrationController@reSendOTP');

    //Route::post('verify-otp', 'API\V1\RegistrationController@verifyOTPForLogin');
    Route::group(['middleware' => ['otp.verify']], function () {
        Route::post('verify-otp', 'API\V1\RegistrationController@verifyOTPForLogin');
    });

    Route::post('register', 'API\V1\RegistrationController@register');
    Route::post('test-bonus', 'API\V1\RegistrationController@testBonus');


    Route::post('validate-number', 'API\V1\RegistrationController@validateNumber');
    Route::post('login', 'API\V1\RegistrationController@login');
    Route::post('logout', 'API\V1\RegistrationController@logout');
    Route::post('refresh', 'API\V1\RegistrationController@getRefreshToken');
    Route::post('set-password', 'API\V1\CustomerController@setPassword');

    Route::group(['middleware' => ['idp.verify']], function () {
        Route::post('change-password', 'API\V1\RegistrationController@changePassword');
    });


    Route::post('forget-password', 'API\V1\RegistrationController@forgetPassword');
    Route::post('api-token', 'API\V1\RegistrationController@getToken');
    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::get('user', 'API\V1\RegistrationController@getAuthenticatedUser');
    });
});


Route::group(['prefix' => 'v1'/*, 'middleware' => ['log.request']*/], function () {

    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::get('test', 'API\V1\DummyAPIController@index');
    });
});


Route::group(['prefix' => 'v1'/*, 'middleware' => ['log.request']*/], function () {

    /**
     * Dashboard APIs
     */
    Route::get('welcome-info', 'API\V1\WelcomeController@getWelcomeInfo');

    Route::get('slider/home', 'API\V1\SliderController@getHomeSliderInfo');
    Route::get('slider/dashboard', 'API\V1\SliderController@getDashboardSliderInfo');
    Route::get('slider/internet', 'API\V1\SliderController@getInternetSliderInfo');
    Route::get('slider/bundle', 'API\V1\SliderController@getBundleSliderInfo');
    Route::get('slider/history', 'API\V1\SliderController@getHistorySliderInfo');
    Route::get('slider/minute', 'API\V1\SliderController@getMinuteSliderInfo');
    Route::get('slider/sms', 'API\V1\SliderController@getSMSSliderInfo');
    Route::get('slider/call-rate', 'API\V1\SliderController@getCallRateSliderInfo');
    Route::get('slider/recharge-offer', 'API\V1\SliderController@getRechargeOfferSliderInfo');
    Route::get('slider/amar-offer', 'API\V1\SliderController@getAmarOfferSliderInfo');

    Route::get('banner', 'API\V1\BannerController@getBannerInfo');

    Route::get('filter/internet-pack', 'API\V1\InternetPackFilterController@getFilters');
    Route::get('internet-pack', 'API\V1\ProductController@getInternetPack');
    Route::get('gift-internet-pack', 'API\V1\ProductController@getGiftInternetPacks');
    Route::post('gift-internet-pack', 'API\V1\ProductController@giftInternetPack');

    Route::get('transfer-internet-pack', 'API\V1\ProductController@getTransferInternetPacks');
    Route::post('transfer-internet-pack', 'API\V1\ProductController@transferInternetPack');

    Route::get('offer/recharge-offer', 'API\V1\ProductController@getRechargeOffer');
    Route::get('offer/rate-cutter-offer', 'API\V1\ProductController@getRateCutterOffer');

    Route::get('offer/mixed-bundle-offer', 'API\V1\ProductController@getMixBundle');
    Route::get('filter/mixed-bundle-offer', 'API\V1\ProductController@mixedBundleOfferFilters');

    Route::get('offer/voice-bundle-offer', 'API\V1\ProductController@getVoiceBundle');
    Route::get('offer/sms-bundle-offer', 'API\V1\ProductController@getSmsBundle');


    Route::get('offer/nearby-offer/{lat}/{long}', 'API\V1\OfferController@nearbyOffer');
    Route::get('amar-offer', 'API\V1\AmarOfferController@getAmarOfferList');
    Route::post('amar-offer/buy', 'API\V1\AmarOfferController@buyAmarOffer');


    /**
     * Shortcut APIs
     */
    Route::get('shortcut', 'API\V1\ShortcutController@getShortcutWithUser');
    Route::post('shortcut', 'API\V1\ShortcutController@addShortcutToUserProfile');
    Route::post('shortcut/remove', 'API\V1\ShortcutController@removeShortcutFromUserProfile');
    Route::post('shortcut/arrange', 'API\V1\ShortcutController@arrangeShortcut');

    /**
     * Usage History
     */
    Route::get('usage-history', 'API\V1\UsageHistory\SummaryUsageHistoryController@getSummaryHistory');

/*    Route::get('usage-history/total-amount', 'API\V1\UsageHistory\SummaryUsageHistoryController@getTotalUsageAmount');*/

    Route::get('usage-history/call', 'API\V1\UsageHistory\CallUsageHistoryController@getCallUsageHistory');
    Route::get('usage-history/sms', 'API\V1\UsageHistory\SmsUsageHistoryController@getSmsUsageHistory');
    Route::get('usage-history/internet', 'API\V1\UsageHistory\InternetUsageHistoryController@getInternetUsageHistory');
    Route::get(
        'usage-history/subscription',
        'API\V1\UsageHistory\SubscriptionUsageHistoryController@getSubscriptionUsageHistory'
    );
    Route::get('usage-history/recharge', 'API\V1\UsageHistory\RechargeHistoryController@getRechargeHistory');

    Route::get('usage-history/roaming/call', 'API\V1\UsageHistory\RoamingUsageHistoryController@getCallUsageHistory');
    Route::get('usage-history/roaming/sms', 'API\V1\UsageHistory\RoamingUsageHistoryController@getSmsUsageHistory');
    Route::get(
        'usage-history/roaming/internet',
        'API\V1\UsageHistory\RoamingUsageHistoryController@getDataUsageHistory'
    );
    Route::get('usage-history/roaming', 'API\V1\UsageHistory\RoamingUsageHistoryController@getSummaryUsageHistory');

    /* Route::get('usage-history/{param}/{roaming_type?}', 'API\V1\DummyAPIController@getUsagesDetails');*/
    // Route::get('usage-history/roaming', 'API\V1\DummyAPIController@getRoamingUsageSummary');
    /* Route::get('recharge-history', 'API\V1\DummyAPIController@getRechargeHistory');*/


    Route::get('current-balance', 'API\V1\CurrentBalanceController@getCurrentBalance');
    Route::get('ussd-code', 'API\V1\UssdCodeController@getUssdCode');


    /**
     * Contextual Cards
     */

    Route::get('contextual-card', 'API\V1\ContextualCardController@getContextualCardInfo');


    /**
     * Notifications
     */

    Route::get('notification', 'API\V1\NotificationController@getNotificationList');


    /**
     * Priyojon Dummy APIs
     */
    Route::get('priyojon/status', 'API\V1\PriyojonController@getPriyojonStatus');
/*    Route::get('priyojon/rewards', 'API\V1\DummyAPIController@getPriyojonRewards');
    Route::get('reedem-priyojon-points', 'API\V1\DummyAPIController@getRedeemPriyojonPoints');*/

    /**
     * Balance APIs
     */
    Route::get('balance/details/{type}', 'API\V1\CurrentBalanceController@getBalanceDetails');
    Route::get('balance/summary', 'API\V1\CurrentBalanceController@getBalanceSummary');
    Route::post('loan/request', 'API\V1\DummyAPIController@requestAdvancedLoan');


    /**
     *  Balance transfer
     */
    Route::post('balance-transfer/set-pin', 'API\V1\CustomerController@generateCustomerPin');
    Route::post('balance-transfer', 'API\V1\CurrentBalanceController@transferBalance');
    Route::post('balance-transfer/change-pin', 'API\V1\CustomerController@changeCustomerPin');


    /**
     *  Digital Services
     */
    Route::get('services', 'API\V1\DummyAPIController@getActiveServices');
    Route::get('digital-services/subscribed', 'API\V1\DummyAPIController@getUserSubscribedServices');

    /**
     *  Customer Cares
     */
    Route::get('customer-cares', 'API\V1\DummyAPIController@getCustomerCares');

    /**
     *  Firebase Push Notification
     */
    Route::post('/save-device-token', 'API\V1\CustomerController@saveDeviceToken');
    Route::post('/push/notification', 'API\V1\PushNotificationController@sendPushNotification')->middleware('checkPushRequestHeader');
    Route::get('/push/notification/{id?}', 'API\V1\PushNotificationController@getNotificationDetails')
        ->middleware('checkPushRequestHeader');


    /**
     *  Notification list by user
     */
    Route::get('notifications', 'API\V1\NotificationController@getNotificationByUser');
    Route::get('notifications/category', 'API\V1\NotificationController@getNotificationCategory');
    Route::get('notifications/count', 'API\V1\NotificationController@getNotificationCount');

    Route::post('notifications/read/all', 'API\V1\NotificationController@markReadAllNotifications');
    Route::post('notifications/read', 'API\V1\NotificationController@markReadNotifications');

    Route::post('notifications/delete/all', 'API\V1\NotificationController@deleteAllNotifications');
    Route::post('notifications/delete', 'API\V1\NotificationController@deleteNotifications');

    Route::get('notifications/preference', 'API\V1\NotificationController@getUserNotificationPreference');
    Route::post('notifications/preference', 'API\V1\NotificationController@updateNotificationPreference');

    Route::post('notifications/preference/reset', 'API\V1\NotificationController@resetNotificationPreference');

    /**
     * Customers
     */
    Route::post('customers', 'API\V1\CustomerController@store');
    Route::get('customers/details', 'API\V1\CustomerController@getDetails');
     Route::get('customers/basic-info', 'API\V1\CustomerController@getCustomerBasicInfo');
    Route::get('customers/profile-image', 'API\V1\CustomerController@getCustomerProfileImage');
    Route::post('customers/update', 'API\V1\CustomerController@updateDetails');
    Route::get('customers/sim-information', 'API\V1\CustomerController@getCustomerSimInfo');
    Route::get('customers/baring-service', 'API\V1\CustomerController@getCustomerBaringInfo');
    Route::post('customers/report-lost-sim', 'API\V1\CustomerController@reportForLostSim');
    Route::post('customers/device-setting', 'API\V1\CustomerController@deviceSetting');

    /**
     *   TERMS AND CONDITIONS
     */
    Route::get('terms-conditions', 'API\V1\TermsAndConditionsController@get');

    /**
     *  PRIVACY AND POLICY
     */
    Route::get('privacy-policy', 'API\V1\PrivacyAndPolicyController@get');

    /**
     * FAQ
     */
    Route::get('faq', 'API\V1\FaqController@getQuestions');
    Route::get('faq/answer', 'API\V1\FaqController@getAnswer');

    /**
     * App Version
     */
    Route::get('app-version/{platform?}', 'API\V1\AppVersionController@index');


    /**
     * App Launch
     */
    Route::get('app-launch', 'API\V1\AppLaunchController@startAppLaunch');

    /**
     * OTP Config
     */
    Route::get('otp-config', 'API\V1\OtpController@index');

    /**
     * FNF
     */
    Route::get('fnf', 'API\V1\FnfController@index');
    Route::put('manage-fnf', 'API\V1\FnfController@manageFnf');
    Route::put('manage-super-fnf', 'API\V1\FnfController@manageSuperFnf');

    /**
     * Backup & Contact restore
     */
    Route::get('backups', 'API\V1\ContactBackupController@getBackupListsByCustomer');
    Route::post('backups/store', 'API\V1\ContactBackupController@store');
    Route::get('backups/details', 'API\V1\ContactBackupController@getBackupDetails');

    /**
     * SSL Payment Gateway
     */
/*    Route::post('recharge-via-ssl', 'API\V1\SslCommerzController@rechargeViaSsl');
    Route::get('ssl-api', 'API\V1\SslCommerzController@sslApi');
    Route::post('recharge-success', 'API\V1\SslCommerzController@success');
    Route::post('recharge-failure', 'API\V1\SslCommerzController@failure');
    Route::post('recharge-cancel', 'API\V1\SslCommerzController@cancel');
    Route::get('payment-options', 'API\V1\SslCommerzController@getRequestDetails');
    Route::post('payment-submit', 'API\V1\SslCommerzController@paymentRequestSubmit');*/
    Route::post('initiate-payment', 'API\V1\PaymentController@initiatePayment');
    Route::post('recharge/validate-numbers', 'API\V1\PaymentController@validateNumbers');
    Route::get('recharge/prefill-amounts', 'API\V1\PaymentController@getPrefillRechargeAmount');



    /**
     * Purchase
     */
    Route::post('purchase/offer', 'API\V1\PurchaseController@purchaseOffer');
    Route::post('purchase/loan-product', 'API\V1\PurchaseController@purchaseLoanProduct');
    Route::post('purchase/internet-offer', 'API\V1\PurchaseController@purchaseInternet');
    Route::post('purchase/mixed-bundle-offer', 'API\V1\PurchaseController@purchaseMixedBundle');

    /**
     * Link Account
     */
    Route::get('additional-account', 'API\V1\SwitchAccountController@getAdditionalAccountList');
    //Route::post('additional-account', 'API\V1\SwitchAccountController@addAdditionalAccount');
    Route::post('arrange-additional-account', 'API\V1\SwitchAccountController@arrangeAdditionalAccount');
    Route::delete('additional-account', 'API\V1\SwitchAccountController@removeAdditionalAccount');
    Route::delete('remove-link-account', 'API\V1\SwitchAccountController@removeLinkAccount');
    Route::post('ask-permission-add-account', 'API\V1\SwitchAccountController@askPermissionAddAdditionalAccount');
    Route::post('response-permission-add-account', 'API\V1\SwitchAccountController@responsePermissionAddAccount');


    /**
     * lodge a complaint
     */
    Route::get('complaint/categories', 'API\V1\ComplaintController@getComplaintCategories');
    Route::get('complaint/tickets', 'API\V1\ComplaintController@getComplaintTickets');
    Route::post('complaint/tickets', 'API\V1\ComplaintController@createComplaintTickets');
    Route::post('complaint/other-tickets', 'API\V1\ComplaintController@createComplaintOtherTickets');


    // loan products

    Route::get('available-loans', 'API\V1\ProductController@getAvailableLoans');

    // store locators

    Route::get('store-locations', 'API\V1\StoreLocatorController@getNearestStoreLocations');

    Route::get('available-migrate-plans', 'API\V1\MigrationPlanController@getAvailableList');
    Route::post('migrate-plan', 'API\V1\MigrationPlanController@migratePlan');


    // Route::get('test/internet', 'API\V1\InternetPackController@getInternetPacks');

    Route::get('my-bill/current', 'API\V1\MyBillController@getCurrentMyBill');
    Route::get('my-bill/previous', 'API\V1\MyBillController@getPreviousMyBill');

    // doctor bhai apis

    Route::get('my-bill/current', 'API\V1\MyBillController@getCurrentMyBill');


    Route::get('feed/doctor-bhai', 'API\V1\Feed\DoctorBhaiController@getPosts');
    Route::post('feed/doctor-bhai/detail', 'API\V1\Feed\DoctorBhaiController@getDetailPost');
    ;


    // popup mesaage

    //getLaunchPopup
    //Route::get('app-launch/popup', 'API\V1\AppLaunchController@getLaunchPopup');
});
