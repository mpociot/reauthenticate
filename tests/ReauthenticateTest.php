<?php

class ReauthenticateTest extends Orchestra\Testbench\TestCase
{

    public function test_middleware_returns_redirect()
    {
        $middleware = new \Mpociot\Reauthenticate\Middleware\Reauthenticate();
        $closure = function(){};

        /** @var Illuminate\Http\Request $request */
        $request = \Illuminate\Http\Request::create('http://reauthenticate.app/restricted','GET',[
            'password' => 'test'
        ]);
        $request->setSession(app('session.store'));

        /** @var Illuminate\Http\RedirectResponse $result */
        $result = $middleware->handle( $request, $closure);
        $this->assertInstanceOf(Illuminate\Http\RedirectResponse::class, $result);
        $this->assertEquals('http://localhost/auth/reauthenticate',$result->getTargetUrl());
        $this->assertEquals(\Session::get('url.intended'),'http://reauthenticate.app/restricted');
    }

    public function test_middleware_returns_next_with_valid_data()
    {
        \Session::set('reauthenticate.life',\Carbon\Carbon::now()->timestamp);
        \Session::set('reauthenticate.authenticated',true);

        $middleware = new \Mpociot\Reauthenticate\Middleware\Reauthenticate();

        $called = false;
        $closure = function() use(&$called) {
            $called = true;
        };

        /** @var Illuminate\Http\Request $request */
        $request = \Illuminate\Http\Request::create('http://reauthenticate.app/restricted','GET',[
            'password' => 'test'
        ]);
        $request->setSession(app('session.store'));

        /** @var Illuminate\Http\RedirectResponse $result */
        $result = $middleware->handle( $request, $closure);
        $this->assertNotInstanceOf(Illuminate\Http\RedirectResponse::class, $result);
        $this->assertNull($result);
        $this->assertTrue($called);
    }

    public function test_middleware_returns_redirect_with_invalid_data()
    {
        \Session::set('reauthenticate.life',\Carbon\Carbon::minValue()->timestamp);
        \Session::set('reauthenticate.authenticated',true);

        $middleware = new \Mpociot\Reauthenticate\Middleware\Reauthenticate();
        $closure = function(){};

        /** @var Illuminate\Http\Request $request */
        $request = \Illuminate\Http\Request::create('http://reauthenticate.app/restricted','GET',[
            'password' => 'test'
        ]);
        $request->setSession(app('session.store'));

        /** @var Illuminate\Http\RedirectResponse $result */
        $result = $middleware->handle( $request, $closure);
        $this->assertInstanceOf(Illuminate\Http\RedirectResponse::class, $result);
        $this->assertEquals('http://localhost/auth/reauthenticate',$result->getTargetUrl());
        $this->assertEquals(\Session::get('url.intended'),'http://reauthenticate.app/restricted');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('session.driver', 'array');
    }
    
}