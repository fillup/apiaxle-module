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

## Using a Proxy ##
You can configure the library to use a proxy if needed for debugging or otherwise. I've used it with [Charles Proxy]() many times to debug my calls and responses from ApiAxle. To configure it to use a proxy, simply set these attributes in the configuration:
```php
    'proxy_enable' => true,
    'proxy_host' => '127.0.0.1',
    'proxy_port' => '8888',
```

## Using your own CA Certs ##
If you are running in an environment where you have your own CA and want to validate the certs, you can configure this library to specify where to find the CA info or a path to a folder with certs. This uses the curl_setopt features in PHP to set these. In order for them to work though, the ssl_verifypeer setting needs to be ```true```:
```php
    'ssl_verifypeer' => true,
    'ssl_cainfo' => null,
    'ssl_capath' => '/etc/pki/tls/certs/',
```

## Contributing ##
If you are interested in contributing to this library and/or extending it please let me know, I'd love to work with others on this to help consider other use cases and design patterns.

## Exceptions ##
100-199: Configuration Related Exceptions

200 - 299: API call related exceptions
