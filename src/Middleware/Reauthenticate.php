<?php

namespace Mpociot\Reauthenticate\Middleware;

use Closure;
use Mpociot\Reauthenticate\ReauthLimiter;

class Reauthenticate
{
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
        $reauth = new ReauthLimiter($request);

        if (!$reauth->check()) {
            $request->session()->put('url.intended', $request->url());

            return $this->invalidated($request);
        }

        return $next($request);
    }

    /**
     * Handle invalidated auth.
     *
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function invalidated($request)
    {
        $url = config('app.reauthenticate_url', 'auth/reauthenticate');

        return redirect($url);
    }
}
