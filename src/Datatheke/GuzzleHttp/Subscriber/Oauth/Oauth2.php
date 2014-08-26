<?php

namespace Datatheke\GuzzleHttp\Subscriber\Oauth;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Exception\ClientException;

use Datatheke\GuzzleHttp\Subscriber\Oauth\GrantType\GrantTypeInterface;

class Oauth2 implements SubscriberInterface
{
    /**
     * AccessToken
     */
    protected $accessToken;

    /**
     * GrantTypeInterface
     */
    protected $grantType;

    /**
     * GrantTypeInterface
     */
    protected $refreshType;

    /**
     *
     */
    protected $tokenUpdatedCallback;

    public function __construct(GrantTypeInterface $grantType = null, GrantTypeInterface $refreshType = null)
    {
        $this->grantType = $grantType;
        $this->refreshType = $refreshType;
    }

    public function getEvents()
    {
        return [
            'before' => ['onBefore', RequestEvents::SIGN_REQUEST],
            'error'  => ['onError', RequestEvents::EARLY],
        ];
    }

    public function onBefore(BeforeEvent $event)
    {
        $request = $event->getRequest();

        // Only sign requests using "auth"="oauth2"
        if ('oauth2' !== $request->getConfig()->get('auth')) {
            return;
        }

        $this->requestAccessToken();
        $request->setHeader('Authorization', sprintf('Bearer %s', $this->accessToken->getAccessToken()));
    }

    public function onError(ErrorEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ('oauth2' !== $request->getConfig()->get('auth')) {
            return;
        }

        if (401 != $event->getResponse()->getStatusCode()) {
            return;
        }

        if ($request->getConfig()->get('retried')) {
            return;
        }

        $this->requestAccessToken(true);
        $request->getConfig()->set('retried', true);
        $event->intercept($event->getClient()->send($request));
    }

    /**
     * Get AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set access token
     *
     * @param AccessToken $accessToken OAuth2 access token
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;

        if ($this->tokenUpdatedCallback) {
            call_user_func($this->tokenUpdatedCallback, $accessToken);
        }
    }

    public function setAccessTokenUpdatedCallback($callback)
    {
        $this->tokenUpdatedCallback = $callback;
    }

    /**
     * Request access token
     *
     * @return AccessToken Oauth2 access token
     */
    protected function requestAccessToken($forceRefresh = false)
    {
        if (null === $this->accessToken) {
            $this->acquireAccessToken();
        } elseif ($this->accessToken->hasExpired() || $forceRefresh) {
            try {
                $this->refreshToken();
            } catch (ClientException $e) {
                if (400 != $e->getResponse()->getStatusCode()) {
                    throw $e;
                }

                $this->acquireAccessToken();
            }
        }
    }

    protected function acquireAccessToken()
    {
        if (null === $this->grantType) {
            throw new \Exception('Unable to acquire AccessToken without setting grantType in constructor');
        }

        $this->setAccessToken($this->grantType->getAccessToken($this->accessToken));
    }

    protected function refreshToken()
    {
        if (null === $this->refreshType) {
            throw new \Exception('Unable to refresh AccessToken without setting refreshType in constructor');
        }

        $this->setAccessToken($this->refreshType->getAccessToken($this->accessToken));
    }
}
