# Reauthenticate
## Because sometimes, you want that extra layer of security

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)
[![Build Status](https://travis-ci.org/mpociot/reauthenticate.svg)](https://travis-ci.org/mpociot/reauthenticate)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpociot/reauthenticate/badges/quality-score.png?b=master&)](https://scrutinizer-ci.com/g/mpociot/reauthenticate/?branch=master)
[![codecov.io](https://codecov.io/github/mpociot/reauthenticate/coverage.svg?branch=master)](https://codecov.io/github/mpociot/reauthenticate?branch=master)
[![StyleCI](https://styleci.io/repos/45939836/shield?style=flat)](https://styleci.io/repos/45939836)

Reauthenticate users by letting them re-enter their passwords for specific parts of your app (for Laravel 5).



```php
Route::group(['middleware' => ['auth','reauthenticate']], function () {

    Route::get('user/payment', function () {
        // Needs to re-enter password to see this
    });

});
```


## Contents

- [Installation](#installation)
- [Usage](#usage)
- [License](#license)

## Installation

In order to add reauthenticate to your project, just add

    "mpociot/reauthenticate": "~1.0"

to your composer.json. Then run `composer install` or `composer update`.

Or run `composer require mpociot/reauthenticate ` if you prefer that.

## Usage

### Add the middleware to your Kernel

In your `app\Http\Kernel.php` file, add the reauthenticate middleware to the `$routeMiddleware` array.

```php
protected $routeMiddleware = [
    // ...
    'reauthenticate'         => \Mpociot\Reauthenticate\Middleware\Reauthenticate::class,
    // ...
];
```

### Add the routes & views

By default, reauthanticate is looking for a route `auth/reauthenticate` and a view `auth.reauthenticate` that will hold a password field.

An example view can be copied from [here](https://github.com/mpociot/reauthenticate/blob/master/views/reauthenticate.blade.php). Please note that this file needs to be manually copied, because I didn't want to bloat this package with a service provider.

The HTTP controller methods can be used from the `Reauthenticates` trait, so your AuthController looks like this:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Mpociot\Reauthenticate\Reauthenticates;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers, Reauthenticates;
```

Be sure to except the reauthenticate routes from the `guest` middleware.

```php
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout','getReauthenticate','postReauthenticate'] ]);
    }
```

To get started, add these routes to your `routes.php` file:

```php
// Reauthentication routes
Route::namespace('Auth')->group(function () {
    Route::get('auth/reauthenticate', 'LoginController@getReauthenticate');
    Route::post('auth/reauthenticate', 'LoginController@postReauthenticate');
});
```

That's it.

Once the user successfully reauthenticates, the valid login will be stored for 30 minutes by default.

## Optional Configuration

Reauthenticate can be (optionally) configured through your `config/app.php` file. The following keys are supported:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Reauthenticate
    |--------------------------------------------------------------------------
    */
    
    /**
     * The URL to redirect to after re-authentication is successful.
     */
    'reauthenticate_url' => '/custom-url',

    /**
     * The key to use as your re-authentication session key.
     */
    'reauthenticate_key' => 'custom-reauthentication-key',

    /**
     * The time (in minutes) that the user is allowed to access the protected area 
     * before being prompted to re re-authenticate.
     */
    'reauthenticate_time' => 30,
];
```

## License

Reauthenticate is free software distributed under the terms of the MIT license.
