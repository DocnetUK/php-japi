# PHP JSON API Library #

Simple library for building HTTP JSON APIs in PHP.

Sure, I know, there are loads of MVC frameworks out there - and a few very popular ones - that can do this for
you and a lot more besides.

BUT, `php-japi` is designed to ONLY do HTTP JSON APIs, so it's small and fast.

As we expand our Service Orientated Architecture (SOA) at Docnet, we're using this more and more - so I hope it's useful
to someone else ;)

Intended to use HTTP status codes wherever possible for passing success/failure etc. back to the client.

Data/payload is your responsibility!

## Hello, World! ##

Let's assume we want our API to respond on the following URL: `api.example.com/hello/world`

So, here's the JAPI controller we'll need:

```php
<?php
class Hello extends \Docnet\JAPI\Controller
{
    public function worldAction()
    {
        $this->setResponse(array(
            'message' =>'Hello, World!'
        ));
    }
}
```

See the examples folder for a working demo.

## Getting Started ##

### Install with Composer ###

Here's the require line for Composer users...

`"docnet/php-japi": "v1.1.1"`

...or just download and use the src folder.

### Entry Point (index.php) ###

Assuming:

- You've got Apache/whatever set up to route all requests to this file
- An auto-loader is present (like the Composer example here) or you've included all files necessary
- Your controllers are not name spaced and you're happy with our default configuration

then something like this is all the code you need

```php
<?php
require_once('vendor/autoload.php');
$api = new \Docnet\JAPI();
$api->run();
```

See the examples folder for a working demo (api.php).

## Routing ##

The standard routing is quite strict, and (at the time ot writing) expects a controller + action pair for all requests.

e.g. `api.example.com/hello/world`

URLs without a 2-part controller + action pair will result in a 404, such as

- `api.example.com`
- `api.example.com/`
- `api.example.com/controller`

We do conversion to `StudlyCaps` classes and `camelCase` methods, splitting on hyphens and suffix 'Action' for the
method. e.g.

- `api.example.com/hello/world` becomes `Hello::worldAction()`
- `api.example.com/hello-world/long-name` becomes `HelloWorld::longNameAction()`

I seem to recall this is similar to ZF1.

### Static Routes ###

If you have some static routes you want to set up, that's no problem - they also bypass the routing regex code
and so make calls very slightly faster.

Add a single custom route

```php
<?php
$api = new \Docnet\JAPI();
$api->getRouter()->addRoute('/goodbye', 'Hello', 'worldAction');
$api->run();
```

Or set a load of them

```php
<?php
$api = new \Docnet\JAPI();
$api->getRouter()->setRoutes(array(
    '/goodbye'  => array('Hello', 'worldAction'),
    '/testing'  => array('SomeController', 'testAction'),
));
$api->run();
```

### Custom Router ###

If you want to write your own Router class? no problem!

Perhaps you want to route based on HTTP request methods (GET/POST/PUT/DELETE).

There's a Router interface and you can follow and you can change the router through the JAPI object like this:

```php
<?php
$api = new \Docnet\JAPI();
$api->setRouter(new MyAwesomeRouter());
$api->run();
```

## Coding Standards ##

Desired adherence to [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).
