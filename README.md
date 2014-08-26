Datatheke PHP client
====================

About
-----
A PHP client for the Data Management System Datatheke ([http://www.datatheke.com/](http://www.datatheke.com/))

Install
-------
Using [composer](https://getcomposer.org/)
```sh
composer require 'datatheke/client' '~0.2.0'
```

Usage
-----
```php
<?php

// src/test.php
$loader = require __DIR__.'/../vendor/autoload.php';

$client = new Datatheke\Api\Client();
$client->setCredentials(MY_USERNAME, MY_PASSWORD);

$libraries = $client->getLibraries();
foreach ($libraries['items'] as $library) {
    echo $library['name']."\n";
}
```