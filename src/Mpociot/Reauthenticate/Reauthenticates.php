<?php

namespace Mpociot\Reauthenticate;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

trait Reauthenticates
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getReauthenticate()
    {
        return View::make('auth.reauthenticate');
    }

    /**
     * Handle the reauthentication request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function postReauthenticate(Request $request)
    {
        $this->validate($request, [
            'password' => 'required',
        ]);
        if (!Hash::check($request->password, Auth::user()->getAuthPassword())) {
            return Redirect::back()
                ->withErrors([
                    'password' => $this->getFailedLoginMessage(),
                ]);
        }

        $request->session()->set('reauthenticate.life', Carbon::now()->timestamp);
        $request->session()->set('reauthenticate.authenticated', true);

        return Redirect::intended();
    }

    /**
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
            ? Lang::get('auth.failed')
            : 'These credentials do not match our records.';
    }
}
