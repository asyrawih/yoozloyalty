<?php

namespace Platform\Controllers\App;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\User;

/**
 * @group Merchant Register
 * 
 */

class VerificationController extends \App\Http\Controllers\Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->only('resend');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Resent the email verification notification
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response(['message' => 'Already verified']);
        }

        $request->user()->sendEmailVerificationNotification();

        if ($request->wantsJson()) {
            return response(['message' => 'Email Sent']);
        }

        return back()->with('resent', true);
    }     

    /**
     * Mark the authenticated user's email address as verified
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response 
     * @param \Illuminate\Auth\Access\AuthorizationException 
     */

     public function verify(Request $request)
     {
        $user = User::find($request->route('id'));
        

        if ($user->hasVerifiedEmail()) {
            return redirect('/go#/login');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($request->user()));
            return redirect('/go#/verification/success');
        }

        return redirect('/go#/login');        
     }
}
