<?php

namespace Datatheke\GuzzleHttp\Subscriber\Oauth\GrantType;

use Datatheke\GuzzleHttp\Subscriber\Oauth\AccessToken;

/**
 * OAuth2 grant type
 */
interface GrantTypeInterface
{
    /**
     * Get access token
     *
     * @return AccessToken
     */
    public function getAccessToken(AccessToken $accessToken = null);
}
