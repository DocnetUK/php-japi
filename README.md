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

## Hello, World! ##

Let's assume we want our API to respond on the following URL: `api.example.com/hello/world`

So, here's the JAPI controller we'll need:

```php
<?php
class Hello extends \Docnet\JAPI\Controller
{
    public function dispatch()
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

Here's the require line for Composer users...

`"docnet/php-japi": "v2.0.0"`

...or just download and use the src folder.

### Entry Point (index.php) ###

Assuming:

- You've got Apache/whatever set up to route all requests to this file
- An auto-loader is present (like the Composer example here) or you've included all files necessary
- Your controllers are not name spaced and you're happy with our default configuration

then something like this is all the code you need

```php
<?php
// @todo update for v2
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
