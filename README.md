[![Build Status](https://api.travis-ci.org/DocnetUK/php-japi.svg?branch=2.0)](https://travis-ci.org/DocnetUK/php-japi)

# PHP JSON API Library #

Version 2 of our library for building HTTP JSON APIs in PHP.

`php-japi` is designed to ONLY do HTTP JSON APIs, so it's small and fast.

Some major changes in version 2
- Adopt better code practices, allowing for Dependency Injection
- Adopt our new "Single Responsibility Controller" approach
- Decouple Router from JAPI container
- Use PSR logging
- Adopt PHP 5.4 minimum version

As we expand our Service Orientated Architecture (SOA) at Docnet, we're using this more and more - so I hope it's useful
to someone else ;)

Intended to use HTTP status codes wherever possible for passing success/failure etc. back to the client.

## Single Responsibility Controller ##

We've adopted a new (for us) take on routing and controller complexity in 2.0. As such, where previously, you might have 
had multiple actions (methods) on the same class like this:

- `BasketController::fetchDetailAction()`
- `BasketController::addAction()`
- `BasketController::removeAction()`
- `BasketController::emptyAction()`

Now this would be 4 name-spaced classes, like this

- `Basket\FetchDetail`
- `Basket\Add`
- `Basket\Remove`
- `Basket\Empty`

This allows for 
- Greater code modularity
- Smaller classes
- Much easier Dependency Injection via `__construct()`

You can still share common code via extension/composition - whatever takes your fancy!

### Routing ###

The bundled router will accept any depth of controller namespace, like this

- `/one` => `One`
- `/one/two` => `One\Two`
- `/one/two/three` => `One\Two\Three`

When you construct the Router, you can give it a "root" namspace, like this:

```php
$router = new \Docnet\JAPI\SolidRouter('\\Docnet\\App\\Controller\\');
```

Which results in this routing:

- `/one/two` => `\Docnet\App\Controller\One\Two`

## Hello, World! ##

Let's assume we want our API to respond on the following URL: `api.example.com/hello/world`

So, here's a JAPI controller we'll need:

```php
namespace Hello;
class World extends \Docnet\JAPI\Controller
{
    public function dispatch() // <-- method declared Abstract in the JAPI Controller
    {
        $this->setResponse([
            'message' =>'Hello, World!'
        ]);
    }
}
```

See the examples folder for a working demo.

## Getting Started ##

### Install with Composer ###

Here's the require line for Composer users (during 2-series development)...

`"docnet/php-japi": "2.0.*@dev"`

...or just download and use the src folder.

### Entry Point (index.php) ###

Assuming:

- You've got Apache/whatever set up to route all requests to this file
- An auto-loader is present (like the Composer example here) or you've included all files necessary
- Your controllers are not name spaced and you're happy with our default configuration

then something like this is all the code you need

```php
(new \Docnet\JAPI())->bootstrap(function(){

    $obj_router = new \Docnet\JAPI\SolidRouter();
    $obj_router->route();

    $str_controller = $obj_router->getController();
    return new $str_controller();

});
```

See the examples folder for a working demo (api.php).

### Static Routes ###

If you have some static routes you want to set up, that's no problem - they also bypass the routing regex code
and so make calls very slightly faster.

Add a single custom route

```php
<?php
// @todo update for v2
```

Or set a load of them

```php
<?php
// @todo update for v2
```

### Custom Router ###

If you want to write your own Router class? no problem!

Perhaps you want to route based on HTTP request methods (GET/POST/PUT/DELETE).

There's a Router interface and you can follow and you can change the router through the JAPI object like this:

```php
<?php
// @todo update for v2
```

## Coding Standards ##

Desired adherence to [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).
