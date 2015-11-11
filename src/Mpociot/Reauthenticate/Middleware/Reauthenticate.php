<?php

namespace Mpociot\Reauthenticate\Middleware;

use Carbon\Carbon;
use Closure;

class Reauthenticate
{
    /**
     * Number of minutes a successful Reauthentication is valid.
     *
     * @var int
     */
    protected $reauthTime = 30;

    /**
     * Validate a reauthenticated Session data.
     *
     * @param \Illuminate\Session\Store $session
     *
     * @return bool
     */
    private function validAuth($session)
    {
        $validationTime = Carbon::createFromTimestamp($session->get('reauthenticate.life', 0));

        return ($session->get('reauthenticate.authenticated', false) &&
            ($validationTime->diffInMinutes() <= $this->reauthTime));
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->validAuth($request->session())) {
            $request->session()->set('url.intended', $request->url());

            return redirect('auth/reauthenticate');
        }

        return $next($request);
    }
}
