<?php

namespace Mpociot\Reauthenticate;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ReauthLimiter
{
    /**
     * The HTTP request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The Reauthentication key.
     *
     * @var string
     */
    protected $key = 'reauthenticate';

    /**
     * Number of minutes a successful Reauthentication is valid.
     *
     * @var int
     */
    protected $reauthTime = 30;

    /**
     * Create a new reauth limiter instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $key
     */
    public function __construct(Request $request, $key = null)
    {
        $this->request = $request;
        $this->key = $key ?: $this->key;
    }

    /**
     * Attempt to Reauthenticate the user.
     *
     * @param string $password
     *
     * @return bool
     */
    public function attempt($password)
    {
        if (!Hash::check($password, Auth::user()->getAuthPassword())) {
            return false;
        }

        $this->request->session()->set($this->key.'.life', Carbon::now()->timestamp);
        $this->request->session()->set($this->key.'.authenticated', true);

        return true;
    }

    /**
     * Validate a reauthenticated Session data.
     *
     * @return bool
     */
    public function check()
    {
        $session = $this->request->session();
        $validationTime = Carbon::createFromTimestamp($session->get($this->key.'.life', 0));

        return $session->get($this->key.'.authenticated', false) &&
            ($validationTime->diffInMinutes() <= $this->reauthTime);
    }
}
