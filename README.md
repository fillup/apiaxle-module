# ApiAxle Module #
[![Travis-CI Build Status](https://api.travis-ci.org/fillup/apiaxle-module.png?branch=master)](https://travis-ci.org/fillup/apiaxle-module) [![Coverage Status](https://coveralls.io/repos/fillup/apiaxle-module/badge.png)](https://coveralls.io/r/fillup/apiaxle-module)

## Introduction ##
[ApiAxle](http://apiaxle.com)
The Open Source API Management Platform

ApiAxle is a proxy that sits on your network, in front of your API(s) and manages things that you shouldn't have to, like rate limiting, authentication and caching. It's fast, open and easy to configure.

## Requirements ##
* PHP >= 5.3.3
* [curl](http://php.net/curl)
* [ApiAxle](http://apiaxle.com) proxy with the apiaxle-api package installed

## Special Note about ApiAxle version 1.11 ##
The tests for this library are not all able to run successfully with ApiAxle version 1.11. I'm working with the ApiAxle team to figure out what changed to cause the problem, but for now I recommend if you are using this library to use ApiAxle 1.10.

## Features / Goals / TODO ##
- [x] Readily consumable, configuration based, object oriented library
- [x] Support for Api objects
- [x] Support for Key objects
- [x] Support for Keyring objects
- [x] Set up continuous integration with Travis-CI: (https://travis-ci.org/fillup/apiaxle-module)
- [ ] Create objects to represent stats, charts, etc?
- [ ] Fix serialization of PHP boolean to string for ApiAxle API. Currently API wants string of either true or false, but serializing PHP boolean results in 0 or 1.
- [ ] Add support for new features in ApiAxle 1.11 to support capturing path statistics

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

4) Use the library however you need. See examples below.

## Usage Examples ##
1) Create a new API, create a new Key, give Key access to API
```php
<?php
    include_once 'vendor/autoload.php';
    
    use ApiAxle\Api\Api;
    use ApiAxle\Api\Key;

    $apiDetails = array(
        'endPoint' => 'api.mydomain.com/api',
        'protocol' => 'https',
    );    
    
    $api = new Api();
    $api->create('myapi',$apiDetails);

    $keyDetails = array(
        'sharedSecret' => 'thisismyreallyshortsecret',
        'qps' => 10,
        'qpd' => 1000,
    );
    
    $key = new Key();
    $key->create('keyvalue',$keyDetails);
    
    $api->linkKey($key);

    // Thats it, your API is registered with API axle, your Key is created,
    // and your Key has access to the new API
```

## API Documentation ##
API documentation generated from phpDocs by apigen is available in the docs/ folder.

## Contributing ##
If you are interested in contributing to this library and/or extending it please let me know, I'd love to work with others on this to help consider other use cases and design patterns.

## Exceptions ##
100-199: Configuration Related Exceptions

200 - 299: API call related exceptions
