ScnHttpCache
============

Adds several features to help make ZF2 applications cacheable.

[![Build Status](https://travis-ci.org/SocalNick/ScnHttpCache.png?branch=master)](https://travis-ci.org/SocalNick/ScnHttpCache)

Requirements
------------
* [Zend Framework 2](https://github.com/zendframework/zf2) (2.*)

Features
--------
* PHP implementation of a gateway cache (COMING SOON)
* Plugin for manipulating HTTP Cache headers (COMING SOON)
* Plugin for dealing with expiration and validation (COMING SOON)
* ESI View Helper
  * Very performant in Surrogate Capability mode - just renders ESI tag
  * Works w/o surrogate capability by running separate application lifecycle


Installation
------------
It is recommended to add this module to your Zend Framework 2 application using Composer. After cloning [ZendSkeletonApplication](https://github.com/zendframework/ZendSkeletonApplication), add "socalnick/scn-http-cache" to list of requirements, then run php composer.phar install/update. Your composer.json should look something like this:
```
{
    "name": "zendframework/skeleton-application",
    "description": "Skeleton Application for ZF2",
    "license": "BSD-3-Clause",
    "keywords": [
        "framework",
        "zf2"
    ],
    "homepage": "http://framework.zend.com/",
    "require": {
        "php": ">=5.3.3",
        "zendframework/zendframework": "2.*",
        "socalnick/scn-http-cache": "1.*"
    }
}
```

Next add the required modules to config/application.config.php:
```php
<?php
return array(
    'modules' => array(
        'Application',
        'ScnHttpCache',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),
    ),
);
```

Varnish
-------

### Installation

Varnish can be installed on any modern Linux distribution: https://www.varnish-cache.org/docs/3.0/installation/ It is also available via [Homebrew](http://mxcl.github.com/homebrew/) on Mac OSX for development by running *brew install varnish*

### Configuration

This is the most basic Varnish configuration for a development environment. It sets the backend host / port, sets a request header indicating Surrogate Capability, and looks for the response Surrogate Control header to initiate ESI handling. Before running Varnish in a production environment, I highly encourage you to learn more about it at https://www.varnish-cache.org/docs

```
backend default {
    .host = "127.0.0.1";
    .port = "10088";
}

sub vcl_recv {
    # Set a header announcing Surrogate Capability to the origin
    # ScnEsiWidget sees this header and emits ESI tag for widgets
    set req.http.Surrogate-Capability = "varnish=ESI/1.0";
}

sub vcl_fetch {
    # Unset the Surrogate Control header and do ESI
    if (beresp.http.Surrogate-Control ~ "ESI/1.0") {
        unset beresp.http.Surrogate-Control;
        set beresp.do_esi = true;
    }
}
```

Usage
-----

### Echo ESI in View Script

```
<div><?php echo $this->esi($this->url('route/to/recent/tweets')) ?></div>
```

### Make the ESI Action

```php
public function recentTweetsAction()
{
    $headers = $this->getResponse()->getHeaders();
    $cacheControl = new \Zend\Http\Header\CacheControl();
    $cacheControl->addDirective('s-maxage', '10');
    $headers->addHeader($cacheControl);

    $viewModel = new ViewModel();
    $viewModel->setTerminal(true);

    return $viewModel;
}
```

### Make a View Script for ESI Widget Action

```
<ul>
    <li><?php echo date('h:i:s')?> @SocalNick: This is a recent tweet!</li>
    <li><?php echo date('h:i:s', time() - 10)?> @SocalNick: This is a slightly less recent tweet!</li>
</ul>
```
