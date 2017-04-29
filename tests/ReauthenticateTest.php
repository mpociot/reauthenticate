<?php

class ReauthenticateTest extends Orchestra\Testbench\TestCase
{
    public function test_middleware_returns_redirect()
    {
        $middleware = new \Mpociot\Reauthenticate\Middleware\Reauthenticate();
        $closure = function () {
        };

        /** @var Illuminate\Http\Request $request */
        $request = \Illuminate\Http\Request::create('http://reauthenticate.app/restricted', 'GET', [
            'password' => 'test',
        ]);
        $this->setSession($request, app('session.store'));

        /** @var Illuminate\Http\RedirectResponse $result */
        $result = $middleware->handle($request, $closure);
        $this->assertInstanceOf(Illuminate\Http\RedirectResponse::class, $result);
        $this->assertEquals('http://localhost/auth/reauthenticate', $result->getTargetUrl());
        $this->assertEquals(\Session::get('url.intended'), 'http://reauthenticate.app/restricted');
    }

    public function test_middleware_returns_next_with_valid_data()
    {
        \Session::put('reauthenticate.life', \Carbon\Carbon::now()->timestamp);
        \Session::put('reauthenticate.authenticated', true);

        $middleware = new \Mpociot\Reauthenticate\Middleware\Reauthenticate();

        $called = false;
        $closure = function () use (&$called) {
            $called = true;
        };

        /** @var Illuminate\Http\Request $request */
        $request = \Illuminate\Http\Request::create('http://reauthenticate.app/restricted', 'GET', [
            'password' => 'test',
        ]);
        $this->setSession($request, app('session.store'));

        /** @var Illuminate\Http\RedirectResponse $result */
        $result = $middleware->handle($request, $closure);
        $this->assertNotInstanceOf(Illuminate\Http\RedirectResponse::class, $result);
        $this->assertNull($result);
        $this->assertTrue($called);
    }

    public function test_middleware_returns_redirect_with_invalid_data()
    {
        \Session::put('reauthenticate.life', \Carbon\Carbon::minValue()->timestamp);
        \Session::put('reauthenticate.authenticated', true);

        $middleware = new \Mpociot\Reauthenticate\Middleware\Reauthenticate();
        $closure = function () {
        };

        /** @var Illuminate\Http\Request $request */
        $request = \Illuminate\Http\Request::create('http://reauthenticate.app/restricted', 'GET', [
            'password' => 'test',
        ]);
        $this->setSession($request, app('session.store'));

        /** @var Illuminate\Http\RedirectResponse $result */
        $result = $middleware->handle($request, $closure);
        $this->assertInstanceOf(Illuminate\Http\RedirectResponse::class, $result);
        $this->assertEquals('http://localhost/auth/reauthenticate', $result->getTargetUrl());
        $this->assertEquals(\Session::get('url.intended'), 'http://reauthenticate.app/restricted');
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('session.driver', 'array');
    }

    /**
     * Set the session for tests in a backwards compatible way
     *
     * @param \Illuminate\Http\Request $request
     * @param Illuminate\Session\Store $session
     *
     * @return void
     */
    protected function setSession($request, $session)
    {
        if (method_exists($request, 'setLaravelSession')) {
            return $request->setLaravelSession($session);
        }

        return $request->setSession($session);
    }
}
