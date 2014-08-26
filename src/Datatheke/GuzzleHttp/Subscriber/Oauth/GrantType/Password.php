<?php

namespace Datatheke\GuzzleHttp\Subscriber\Oauth\GrantType;

use GuzzleHttp\ClientInterface;

use Datatheke\GuzzleHttp\Subscriber\Oauth\AccessToken;

class Password implements GrantTypeInterface
{
    protected $credentials;

    public function __construct(ClientInterface $client, $credentials)
    {
        if (!is_array($credentials) && !is_callable($credentials)) {
            throw new \Exception('Credentials must be an an array or a callable');
        }

        $this->client = $client;
        $this->credentials = $credentials;
    }

    public function getAccessToken(AccessToken $accessToken = null)
    {
        list($username, $password) = $this->getCredentials();

        $body = [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password
        ];

        $response = $this->client->post(null, ['body' => $body]);
        $data = $response->json();

        return AccessToken::createFromArray($data);
    }

    protected function getCredentials()
    {
        if (is_array($this->credentials)) {
            return $this->credentials;
        }

        return call_user_func($this->credentials);
    }
}
