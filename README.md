# ApiAxle Module #

## Introduction ##
[ApiAxle](http://apiaxle.com)
The Open Source API Management Platform

ApiAxle is a proxy that sits on your network, in front of your API(s) and manages things that you shouldn't have to, like rate limiting, authentication and caching. It's fast, open and easy to configure.

## Requirements ##
* PHP >= 5.3.3
* [curl](http://php.net/curl)
* [ApiAxle](http://apiaxle.com) proxy with the apiaxle-api package installed

## Features / Goals / TODO ##
- [x] Readily consumable, configuration based, object oriented library
- [x] Support for Api objects
- [x] Support for Key objects
- [ ] Support for Keychain objects
- [x] Set up continuous integration with Travis-CI: (https://travis-ci.org/fillup/apiaxle-module)

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

## Contributing ##
If you are interested in contributing to this library and/or extending it please let me know, I'd love to work with others on this to help consider other use cases and design patterns.

## Exceptions ##
100-199: Configuration Related Exceptions

200 - 299: API call related exceptions