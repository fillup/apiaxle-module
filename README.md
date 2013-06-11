# ApiAxle Module #

## Introduction ##
[ApiAxle](http://apiaxle.com)
The Open Source API Management Platform

ApiAxle is a proxy that sits on your network, in front of your API(s) and manages things that you shouldn't have to, like rate limiting, authentication and caching. It's fast, open and easy to configure.

This module is not directly affiliated with ApiAxle but is intended to provide a readily usable PHP library for interacting with the ApiAxle API. It may also be extended to become a simple Zend Framework 2 module with views to interact with the API.

## Requirements ##
* PHP >= 5.3.3
* [curl](http://php.net/curl)
* [ApiAxle](http://apiaxle.com) proxy with the apiaxle-api package installed

## Features / Goals ##
- [ ] Readily consumable, configuration based, object oriented library
- [ ] Simple interface provided as a [ZF2](http://framework.zend.com) module

## Installation ##
### Using Composer - Recommended ###
1) Edit your composer file to include:

```json
{
    "require": {
        "php": ">=5.3.3",
        "fillup/apiaxle": "dev-master"
    }
}
```

2) Update composer install with ```php composer.phar update```

3) Make sure you are including the composer autoload file:

```php
include_once './vendor/autoload.php';
```

4) In progress...

## Contributing ##
If you are interested in contributing to this library and/or extending it please let me know, I'd love to work with others on this to help consider other use cases and design patters.

## Exceptions ##
100-199: Configuration Related Exceptions

200 - 299: API call related exceptions