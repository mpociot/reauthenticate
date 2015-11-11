# Reauthenticate
## Because sometimes, you want that extra layer of security

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/mpociot/reauthenticate.svg)](https://travis-ci.org/mpociot/reauthenticate)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpociot/reauthenticate/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mpociot/reauthenticate/?branch=master)
[![codecov.io](https://codecov.io/github/mpociot/reauthenticate/coverage.svg?branch=master)](https://codecov.io/github/mpociot/reauthenticate?branch=master)

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

<a name="installation" />
## Installation

In order to add reauthenticate to your project, just add 

    "mpociot/reauthenticate": "0.9"

to your composer.json. Then run `composer install` or `composer update`.

Or run `composer require mpociot/reauthenticate ` if you prefer that.

<a name="usage" />
## Usage

### Add the middleware to your Kernel

In your `app\Http\Kernel.php` file, add the reauthenticate middleware to the `$routeMiddleware` array.

```php
protected $routeMiddleware = [
    // ...
    'reauthenticate'         => Mpociot\Reauthenticate\Middleware\Reauthenticate::class,
    // ...
];
```

### Add the routes & views

By default, reauthanticate is looking for a route `auth/reauthenticate` and a view `auth.reauthenticate` that will hold a password field.

The HTTP controller methods can be used from the `Reauthenticates` trait, so your AuthController looks like this:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Mpociot\Reauthenticate\Reauthenticates;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins, Reauthenticates;
```

To get started, add these routes to your `routes.php` file:

```php
// Reauthentication routes
Route::get('auth/reauthenticate', 'Auth\AuthController@getReauthenticate');
Route::post('auth/reauthenticate', 'Auth\AuthController@postReauthenticate');
```

That's it.
Once the user successfully reauthenticates, the valid login will be stored for 30 minutes.

This value can be configured by extending the Reauthenticate middleware.

<a name="license" />
## License

Reauthenticate is free software distributed under the terms of the MIT license.
