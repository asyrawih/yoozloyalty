<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;

class EmailTemplate extends Model
{
    use HasFactory;
    use GeneratesUuid;

    protected $guarded = [];

    public static function insertRecord($userid)
    {
        $datas = [
            [
              'created_by' => $userid,
              'name' => 'customer_forgot_password',
                'subject' => '{{ website_name }} Reset your password' ,
                'template' => '<table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                    <td align="center">
                        <table class="content" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="header">
                                <a href="{{ website_url }}">
                                  {{ website_name }}
                                </a>
                                </td>
                            </tr>
                            <!-- Email Body -->
                            <tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0">
                                <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <!-- Body content -->
                                    <tr>
                                        <td class="content-cell">
                                            <p>You are receiving this email because we received a password reset request for your account. Please click the button below to set a new password.</p>
                                            <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                        <td align="center">
                                                            <table border="0" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        {{ forgot_password_button }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                            <p>If you did not request a password reset, no further action is required. This link is valid for 24 hours.</p>
                                        </td>
                                    </tr>
                                </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="content-cell" align="center">
                                            <p>If you re having trouble clicking the "Reset password" button, copy and paste the URL below into your web browser: {{ forgot_password_url }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    </tr>
                </table>
                <style>
                    @media  only screen and (max-width: 600px) {
                    .inner-body {
                    width: 100% !important;
                    }
                    .footer {
                    width: 100% !important;
                    }
                    }
                    @media  only screen and (max-width: 500px) {
                    .button {
                    width: 100% !important;
                    }
                    }
                </style>
            '
            ],
            [
              'created_by' => $userid,
              'name' => 'staff_forgot_password',
                'subject' => '{{ website_name }} Reset your password' ,
                'template' => '
                <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                    <td align="center">
                        <table class="content" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="header">
                                <a href="{{ website_url }}">
                                  {{ website_name }}
                                </a>
                                </td>
                            </tr>
                            <!-- Email Body -->
                            <tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0">
                                <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <!-- Body content -->
                                    <tr>
                                        <td class="content-cell">
                                            <p>You are receiving this email because we received a password reset request for your account. Please click the button below to set a new password.</p>
                                            <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                        <td align="center">
                                                            <table border="0" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        {{ forgot_password_button }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                            <p>If you did not request a password reset, no further action is required. This link is valid for 24 hours.</p>
                                        </td>
                                    </tr>
                                </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="content-cell" align="center">
                                            <p>If you re having trouble clicking the "Reset password" button, copy and paste the URL below into your web browser: {{ forgot_password_url }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    </tr>
                </table>
                <style>
                @media  only screen and (max-width: 600px) {
                .inner-body {
                width: 100% !important;
                }
                .footer {
                width: 100% !important;
                }
                }
                @media  only screen and (max-width: 500px) {
                .button {
                width: 100% !important;
                }
                }
            </style>
            '
            ],
            [
              'created_by' => $userid,
              'name' => 'customer_registeration',
                'subject' => 'Welcome {{ name_of_user }} to {{ website_name }}' ,
                'template' => '
                <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                    <td align="center">
                        <table class="content" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="header">
                                <a href="{{ website_url }}">
                                  {{ website_name }}
                                </a>
                                </td>
                            </tr>
                            <!-- Email Body -->
                            <tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0">
                                <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <!-- Body content -->
                                    <tr>
                                        <td class="content-cell">
                                            <p>You are receiving this email because you registered to {{ website_name }}</p>
                                            <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                        <td align="center">
                                                            <table border="0" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        {{ verification_button }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="content-cell" align="center">
                                            <p>If you re having trouble clicking the "Login" button, copy and paste the URL below into your web browser: {{ verification_url }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    </tr>
                </table>
                <style>
                @media  only screen and (max-width: 600px) {
                .inner-body {
                width: 100% !important;
                }
                .footer {
                width: 100% !important;
                }
                }
                @media  only screen and (max-width: 500px) {
                .button {
                width: 100% !important;
                }
                }
            </style>
            '
            ],
            [
              'created_by' => $userid,
              'name' => 'staff_registeration',
                'subject' => 'Welcome {{ name_of_user }} to {{ website_name }}' ,
                'template' => '
                <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                    <td align="center">
                        <table class="content" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="header">
                                <a href="{{ website_url }}">
                                  {{ website_name }}
                                </a>
                                </td>
                            </tr>
                            <!-- Email Body -->
                            <tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0">
                                <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <!-- Body content -->
                                    <tr>
                                        <td class="content-cell">
                                            <p>You are receiving this email because you registered to {{ website_name }}</p>
                                            <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                        <td align="center">
                                                            <table border="0" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        {{ login_button }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="content-cell" align="center">
                                            <p>If you re having trouble clicking the "Login" button, copy and paste the URL below into your web browser: {{ login_url }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    </tr>
                </table>
                <style>
                @media  only screen and (max-width: 600px) {
                .inner-body {
                width: 100% !important;
                }
                .footer {
                width: 100% !important;
                }
                }
                @media  only screen and (max-width: 500px) {
                .button {
                width: 100% !important;
                }
                }
            </style>
            '
              ],
              [
                'created_by' => $userid,
                'name' => 'customer_credit_point',
                  'subject' => 'Congratulation you got {{ point_got }} by {{ event }}' ,
                  'template' => '
                  <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                  <td align="center">
                      <table class="content" width="100%" cellpadding="0" cellspacing="0">
                          <tr>
                              <td class="header">
                              <a href="{{ website_url }}">
                                {{ website_name }}
                              </a>
                              </td>
                          </tr>
                          <!-- Email Body -->
                          <tr>
                              <td class="body" width="100%" cellpadding="0" cellspacing="0">
                              <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                                  <!-- Body content -->
                                  <tr>
                                      <td class="content-cell">
                                          <p>
                                            You got coupon {{ point_got }} by {{ event }} your point now is {{ current_point }}
                                          </p>
                                          <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                        <td align="center">
                                                            <table border="0" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        {{ login_button }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                          </table>
                                      </td>
                                  </tr>
                              </table>
                              </td>
                          </tr>
                          <tr>
                              <td>
                              <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                  <tr>
                                      <td class="content-cell" align="center">
                                          <p>If you re having trouble clicking the "Login" button, copy and paste the URL below into your web browser: {{ login_url }}
                                          </p>
                                      </td>
                                  </tr>
                              </table>
                              </td>
                          </tr>
                      </table>
                  </td>
                  </tr>
              </table>
              <style>
              @media  only screen and (max-width: 600px) {
              .inner-body {
              width: 100% !important;
              }
              .footer {
              width: 100% !important;
              }
              }
              @media  only screen and (max-width: 500px) {
              .button {
              width: 100% !important;
              }
              }
          </style>
              '
                ],
              [
                'created_by' => $userid,
                'name' => 'customer_redeem_point',
                  'subject' => 'Congratulation you got {{ reward }}' ,
                  'template' => '
                  <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                  <td align="center">
                      <table class="content" width="100%" cellpadding="0" cellspacing="0">
                          <tr>
                              <td class="header">
                              <a href="{{ website_url }}">
                                {{ website_name }}
                              </a>
                              </td>
                          </tr>
                          <!-- Email Body -->
                          <tr>
                              <td class="body" width="100%" cellpadding="0" cellspacing="0">
                              <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                                  <!-- Body content -->
                                  <tr>
                                      <td class="content-cell">
                                          <p>
                                        You got {{ reward }} your point now is {{ current_point }}
                                         </p>
                                      </td>
                                  </tr>
                              </table>
                              </td>
                          </tr>
                          <tr>
                              <td>
                              <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                  <tr>
                                      <td class="content-cell" align="center">
                                          <p>If you re having trouble clicking the "Login" button, copy and paste the URL below into your web browser: {{ login_url }}
                                          </p>
                                      </td>
                                  </tr>
                              </table>
                              </td>
                          </tr>
                      </table>
                  </td>
                  </tr>
              </table>
              <style>
              @media  only screen and (max-width: 600px) {
              .inner-body {
              width: 100% !important;
              }
              .footer {
              width: 100% !important;
              }
              }
              @media  only screen and (max-width: 500px) {
              .button {
              width: 100% !important;
              }
              }
          </style>'
                ],
              [
                'created_by' => $userid,
                'name' => 'customer_otp',
                  'subject' => 'Confirmation needed for reward redemption' ,
                  'template' => '
                  <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                  <td align="center">
                      <table class="content" width="100%" cellpadding="0" cellspacing="0">
                          <tr>
                              <td class="header">
                              <a href="{{ website_url }}">
                                {{ website_name }}
                              </a>
                              </td>
                          </tr>
                          <!-- Email Body -->
                          <tr>
                              <td class="body" width="100%" cellpadding="0" cellspacing="0">
                              <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                                  <!-- Body content -->
                                  <tr>
                                      <td class="content-cell">
                                          <p>
                                            Please use this code {{ otp }} for redeeming your reward. Do not share this code with anyone.
                                         </p>
                                      </td>
                                  </tr>
                              </table>
                              </td>
                          </tr>
                          <tr>
                              <td>
                              <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                  <tr>
                                      <td class="content-cell" align="center">
                                          <p>If you re having trouble clicking the "Login" button, copy and paste the URL below into your web browser: {{ login_url }}
                                          </p>
                                      </td>
                                  </tr>
                              </table>
                              </td>
                          </tr>
                      </table>
                  </td>
                  </tr>
              </table>
              <style>
              @media  only screen and (max-width: 600px) {
              .inner-body {
              width: 100% !important;
              }
              .footer {
              width: 100% !important;
              }
              }
              @media  only screen and (max-width: 500px) {
              .button {
              width: 100% !important;
              }
              }
          </style>'
                ],
              [
                'created_by' => $userid,
                'name' => 'customer_coupun_delivery',
                  'subject' => 'Coupon Delivery' ,
                  'template' => '
                  <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                  <td align="center">
                      <table class="content" width="100%" cellpadding="0" cellspacing="0">
                          <tr>
                              <td class="header">
                              <a href="{{ website_url }}">
                                {{ website_name }}
                              </a>
                              </td>
                          </tr>
                          <!-- Email Body -->
                          <tr>
                              <td class="body" width="100%" cellpadding="0" cellspacing="0">
                              <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                                  <!-- Body content -->
                                  <tr>
                                      <td class="content-cell">
                                          <p>
                                          You got {{ coupun_name }} code : {{ coupun_code }}
                                         </p>
                                      </td>
                                  </tr>
                              </table>
                              </td>
                          </tr>
                          <tr>
                              <td>
                              <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                  <tr>
                                      <td class="content-cell" align="center">
                                          <p>If you re having trouble clicking the "Login" button, copy and paste the URL below into your web browser: {{ login_url }}
                                          </p>
                                      </td>
                                  </tr>
                              </table>
                              </td>
                          </tr>
                      </table>
                  </td>
                  </tr>
              </table>
              <style>
              @media  only screen and (max-width: 600px) {
              .inner-body {
              width: 100% !important;
              }
              .footer {
              width: 100% !important;
              }
              }
              @media  only screen and (max-width: 500px) {
              .button {
              width: 100% !important;
              }
              }
          </style>'
                ],
          ];

          foreach ($datas as $data) {
              $model = new \App\Models\EmailTemplate;
              $record = $model->where('name', $data['name'])->where('created_by', $data['created_by'])->first();
              if(!$record){
                  $model->name = $data['name'];
                  $model->subject = $data['subject'];
                  $model->template = $data['template'];
                  $model->created_by = $data['created_by'];
                  $model->save();
              }
          }
    }
}
