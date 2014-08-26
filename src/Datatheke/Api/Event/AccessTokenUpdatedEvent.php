<?php

namespace Datatheke\Api\Event;

use Symfony\Component\EventDispatcher\Event;

use Datatheke\GuzzleHttp\Subscriber\Oauth\AccessToken;

class AccessTokenUpdatedEvent extends Event
{
    protected $accessToken;

    public function __construct(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
