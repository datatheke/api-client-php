<?php

namespace Datatheke\Component\RestApi;

use Guzzle\Http\Client as GuzzleClient;

class Client
{
    const URL_API = 'http://www.datatheke.com/api/v1';

    /**
     * Config
     */
    protected $username;
    protected $password;
    protected $options;

    /**
     * Guzzle\Http\Client
     */
    protected $client;

    /**
     * Access token
     */
    protected $token;

    public function __construct($username, $password, array $options = array())
    {
        $this->username = $username;
        $this->password = $password;
        $this->options  = $options;
    }

    protected function getClient($connected = true)
    {
        if (!$this->client) {
            $this->client = new GuzzleClient(self::URL_API, $this->options);
        }

        if ($connected && !$this->isConnected()) {
            $this->connect();
        }

        return $this->client;
    }

    public function isConnected()
    {
        return null !== $this->token;
    }

    public function connect()
    {
        $client = $this->getClient(false);

        $client->setDefaultHeaders(array());
        $response = $client->post('token')->setAuth($this->username, $this->password)->send();
        $content  = json_decode((string) $response->getBody(), true);
        $this->token = $content['token'];
        $client->setDefaultHeaders(array(
            'Authorization' => 'Bearer '.$this->token,
            'Content-type'  => 'application/json'
            )
        );

        return $this;
    }

    public function getLibraries($page = null)
    {
        $response = $this->getClient()->get('libraries?page='.(int) $page)->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function getLibrary($id)
    {
        $response = $this->getClient()->get(array('library/{id}', array('id' => $id)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function getLibraryCollections($id, $page = null)
    {
        $response = $this->getClient()->get(array('library/{id}/collections?page='.(int) $page, array('id' => $id)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function getCollection($id)
    {
        $response = $this->getClient()->get(array('collection/{id}', array('id' => $id)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function getCollectionItems($id, $page = null)
    {
        $response = $this->getClient()->get(array('collection/{id}/items?page='.(int) $page, array('id' => $id)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function getItem($collectionId, $id)
    {
        $response = $this->getClient()->get(array('collection/{collectionId}/item/{id}', array('collectionId' => $collectionId, 'id' => $id)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function createLibrary($library)
    {
        $response = $this->getClient()->post('library', null, json_encode(array('library' => $library)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function createCollection($libraryId, $collection)
    {
        $response = $this->getClient()->post(array('library/{id}', array('id' => $libraryId)), null, json_encode(array('collection' => $collection)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function createItem($collectionId, $item)
    {
        $response = $this->getClient()->post(array('collection/{id}', array('id' => $collectionId)), null, json_encode(array('_'.$collectionId => $item)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function updateLibrary($libraryId, $library)
    {
        $response = $this->getClient()->put(array('library/{id}', array('id' => $libraryId)), null, json_encode(array('library' => $library)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function updateCollection($collectionId, $collection)
    {
        $response = $this->getClient()->put(array('collection/{id}', array('id' => $collectionId)), null, json_encode(array('collection' => $collection)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function updateItem($collectionId, $itemId, $item)
    {
        $response = $this->getClient()->put(array('collection/{collectionId}/item/{id}', array('collectionId' => $collectionId, 'id' => $itemId)), null, json_encode(array('_'.$collectionId => $item)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function deleteLibrary($id)
    {
        $response = $this->getClient()->delete(array('library/{id}', array('id' => $id)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function deleteCollection($id)
    {
        $response = $this->getClient()->delete(array('collection/{id}', array('id' => $id)))->send();
        return json_decode((string) $response->getBody(), true);
    }

    public function deleteItem($collectionId, $id)
    {
        $response = $this->getClient()->delete(array('collection/{collectionId}/item/{id}', array('collectionId' => $collectionId, 'id' => $id)))->send();
        return json_decode((string) $response->getBody(), true);
    }
}