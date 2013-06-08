Datatheke.com PHP API client
============================

Dependencies
------------

You need to install the [Guzzle library](http://guzzlephp.org/).


Usage
-----

``` php
    require_once('../../api-client-php/Client.php');
    $client = new \Datatheke\Component\RestApi\Client('login', 'password');

    $response = $client->createLibrary(array(
        'name' => 'Test library',
        'description' => 'This is a super library'
    ));

    $response = $client->createCollection($response['id'], array(
        'name' => 'First collection',
        'description' => 'This is my first collection',
        'fields' => array(
            array('label' => 'Name', 'type' => 'string'),
            array('label' => 'Description', 'type' => 'textarea'),
            array('label' => 'Date', 'type' => 'date'),
            array('label' => 'Position', 'type' => 'coordinates')
            )
    ));

    $collection = $client->getCollection($response['id']);

    $response = $client->createItem($collection['id'], array(
        '_'.$collection['fields'][0]['id'] => 'Bob',
        '_'.$collection['fields'][1]['id'] => 'A very cool guy',
        '_'.$collection['fields'][2]['id'] => '01/02/2013',
        '_'.$collection['fields'][3]['id'] => array('longitude' => 2.294619, 'latitude' => 48.858207)
    ));
```