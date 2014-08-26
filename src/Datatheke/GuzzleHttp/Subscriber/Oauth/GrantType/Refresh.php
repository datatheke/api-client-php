<?php

namespace Datatheke\GuzzleHttp\Subscriber\Oauth\GrantType;

use GuzzleHttp\ClientInterface;

use Datatheke\GuzzleHttp\Subscriber\Oauth\AccessToken;

class Refresh implements GrantTypeInterface
{
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getAccessToken(AccessToken $accessToken = null)
    {
        if (null === $accessToken) {
            throw new \Exception('An AccessToken is required');
        }

        $body = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $accessToken->getRefreshToken()
        ];

        $response = $this->client->post(null, ['body' => $body]);
        $data = $response->json();

        return AccessToken::createFromArray($data);
    }
}
