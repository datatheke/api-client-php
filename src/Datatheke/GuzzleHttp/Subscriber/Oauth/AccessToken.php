<?php

namespace Datatheke\GuzzleHttp\Subscriber\Oauth;

class AccessToken
{
    private $accessToken;
    private $refreshToken;
    private $expiresAt;
    private $scope;

    public function __construct($accessToken, $refreshToken, $expiresAt, $scope = null)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->scope = $scope;

        if (!$expiresAt instanceof \DateTime) {
            $expiresAt = (new \DateTime())->setTimestamp($expiresAt);
        }
        $this->expiresAt = $expiresAt;
    }

    public function createFromArray(array $data)
    {
        return new self(
            $data['access_token'],
            $data['refresh_token'],
            (new \DateTime())->modify(sprintf('+%s seconds', $data['expires_in'])),
            isset($data['scope']) ?: null
        );
    }

    public function hasExpired()
    {
        return $this->expiresAt < (new \DateTime());
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function toArray()
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expires_at' => $this->expiresAt->getTimestamp(),
            'scope' => $this->scope
        ];
    }
}
