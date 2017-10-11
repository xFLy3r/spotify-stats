<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
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
        $json = $this->client->request('GET', 'https://api.spotify.com/v1/me', [
            'headers' => [
                'Authorization:' => 'Bearer ' . $token,
                'Accept:' => 'application/json',
                'Content-Type:' => 'application/json',
            ]
        ]);
        $content = json_decode($json->getBody()->getContents(), true);

        return $content;
    }

    protected function getToken()
    {
        /** @var OAuthToken $oAuthToken */
        $oAuthToken = $this->tokenStorage->getToken();

        return $oAuthToken->getAccessToken();
    }
}