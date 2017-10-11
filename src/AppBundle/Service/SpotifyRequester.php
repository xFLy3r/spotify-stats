<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use HWI\Bundle\OAuthBundle\HWIOAuthBundle;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\SpotifyResourceOwner;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SpotifyRequester
{
    protected $client;

    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->client = new Client();
    }

    public function getInfoAboutCurrentUser()
    {
        $token = $this->getToken();
        if ($token === false) {
            return null;
        }
        $response = $this->client->request('GET', 'https://api.spotify.com/v1/me', [
            'headers' => [
                'Authorization:' => 'Bearer ' . $token,
                'Accept:' => 'application/json',
                'Content-Type:' => 'application/json',
            ]
        ]);
        if ($response->getStatusCode() === 401) {
            $response = $this->client->request('GET', '/login/connect-spotify');
        }
        $content = json_decode($response->getBody()->getContents(), true);

        return $content;
    }

    protected function getToken()
    {
        if ($this->tokenStorage->getToken() instanceof OAuthToken) {
            /** @var OAuthToken $oAuthToken */
            $oAuthToken = $this->tokenStorage->getToken();
            return $oAuthToken->getAccessToken();
        }

        return false;
    }

    protected function refreshToken()
    {

    }
}