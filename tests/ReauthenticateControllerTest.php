<?php

use Carbon\Carbon;

class ReauthenticateControllerTest extends Orchestra\Testbench\TestCase
{
    public function test_get_reauthenticate_shows_view()
    {
        $controller = new TestController();
        $this->setExpectedException('InvalidArgumentException', 'View [auth.reauthenticate] not found.');
        $controller->getReauthenticate();
    }

    public function test_post_reauthenticate_returns_error()
    {
        $user = new TestUser();

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        $request = \Illuminate\Http\Request::create('http://reauthenticate.app/auth/reauthenticate', 'POST', [
            'password' => 'test',
        ]);
        $request->setSession(app('session.store'));

        $controller = new TestController();

        /** @var Illuminate\Http\RedirectResponse $response */
        $response = $controller->postReauthenticate($request);
        $this->assertInstanceOf(Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals('http://localhost', $response->getTargetUrl());
    }

    public function test_post_reauthenticate_returns_redirect()
    {
        $user = new TestUser();
        $user->password = bcrypt('test');

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        Session::set('url.intended', 'http://reauthenticate.app/auth/reauthenticate');
        $request = \Illuminate\Http\Request::create('http://reauthenticate.app/auth/reauthenticate', 'POST', [
            'password' => 'test',
        ]);
        $request->setSession(app('session.store'));

        $controller = new TestController();

        /** @var Illuminate\Http\RedirectResponse $response */
        $response = $controller->postReauthenticate($request);
        $this->assertInstanceOf(Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals('http://reauthenticate.app/auth/reauthenticate', $response->getTargetUrl());

        $this->assertTrue(Session::has('reauthenticate.life'));
        $this->assertTrue(Session::has('reauthenticate.authenticated'));
        $this->assertTrue(Session::get('reauthenticate.authenticated'));
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
        $app['config']->set('auth.model', 'TestUser');
    }
}

class TestController extends \Illuminate\Routing\Controller
{
    use Illuminate\Foundation\Validation\ValidatesRequests;
    use \Mpociot\Reauthenticate\Reauthenticates;
}

class TestUser extends \Illuminate\Database\Eloquent\Model
{
    use \Illuminate\Auth\Authenticatable;
}
