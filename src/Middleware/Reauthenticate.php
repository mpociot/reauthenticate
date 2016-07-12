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
            $request->session()->set('url.intended', $request->url());

            return $this->invalidated($request);
        }

        return $next($request);
    }

    /**
     * Handle invalidated auth.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    protected function invalidated($request)
    {
        return redirect('auth/reauthenticate');
    }
}
