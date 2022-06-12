<?php

namespace Platform\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\SettingStore;
use Carbon\Carbon;

class AppController extends \App\Http\Controllers\Controller {

  /*
   |--------------------------------------------------------------------------
   | Single Page Application Controller for Web App
   |--------------------------------------------------------------------------
   |
   | App logic
   |--------------------------------------------------------------------------
   */

  /**
   * Initialize the app
   *
   * @return \Illuminate\View\View
   */
  public function index() {
    $account = app()->make('account');
    $config = $account->config;
    $account = (object) $account->only('version', 'app_name', 'app_headline', 'app_color', 'app_scheme', 'app_host', 'language', 'locale', 'logo');

    if (env('APP_DEMO', false) === true) $account->demo = true;

    $account->color_name = 'indigo accent-4 white--text';
    $account->text_color_name = 'white';

    $store = SettingStore::find(1);

    $website = [
        'logo' => $store->getFirstMediaUrl('logo', 'logo'),
        'favicon' => $store->getFirstMediaUrl('favicon', 'favicon'),
    ];

    $account->logo = $website['logo'];

    return view('app.index', compact('account', 'config', 'website'));
  }
  /**
   * This page is shown when no account is matched with the host
   *
   * @return \Illuminate\View\View
   */
  public function getAccountNotFound() {
    $account = app()->make('account');
    return view('account-404', compact('account'));
  }

}
