<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
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

    private function refreshToken()
    {
        $this->getToken();
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

        $content = json_decode($response->getBody()->getContents(), true);

        return $content;
    }

    public function getFavouriteArtist()
    {
        $token = $this->getToken();
        if ($token === false) {
            return null;
        }

        $response = $this->client->request('GET',
            'https://api.spotify.com/v1/me/top/artists?limit=1&time_range=long_term', [
            'headers' => [
                'Authorization:' => 'Bearer ' . $token,
                'Accept:' => 'application/json',
                'Content-Type:' => 'application/json',
            ]
        ]);

        $content = json_decode($response->getBody()->getContents(), true);

        return $content;
    }

    public function getFavouriteTrack()
    {
        $token = $this->getToken();
        if ($token === false) {
            return null;
        }

        $response = $this->client->request('GET',
            'https://api.spotify.com/v1/me/top/tracks?limit=1&time_range=long_term', [
                'headers' => [
                    'Authorization:' => 'Bearer ' . $token,
                    'Accept:' => 'application/json',
                    'Content-Type:' => 'application/json',
                ]
            ]);

        $content = json_decode($response->getBody()->getContents(), true);

        return $content;
    }

    public function getRecentlyPlayedTrack()
    {
        $token = $this->getToken();
        if ($token === false) {
            return null;
        }

        $response = $this->client->request('GET',
            'https://api.spotify.com/v1/me/player/recently-played?limit=1', [
                'headers' => [
                    'Authorization:' => 'Bearer ' . $token,
                    'Accept:' => 'application/json',
                    'Content-Type:' => 'application/json',
                ]
            ]);

        $content = json_decode($response->getBody()->getContents(), true);

        return $content;
    }

    public function getFavouriteGenre()
    {
        $token = $this->getToken();
        if ($token === false) {
            return null;
        }

        $response = $this->client->request('GET',
            'https://api.spotify.com/v1/me/top/artists?limit=50&time_range=long_term', [
                'headers' => [
                    'Authorization:' => 'Bearer ' . $token,
                    'Accept:' => 'application/json',
                    'Content-Type:' => 'application/json',
                ]
            ]);

        $content = json_decode($response->getBody()->getContents(), true);
        $genres = array();
        foreach ($content['items'] as $artist) {
            foreach ($artist['genres'] as $genre) {
                $genres[] = $genre;
            }
        }

        $genre = array_search(max(array_count_values($genres)), array_count_values($genres));
        return ucwords(strtolower($genre));
    }

    public function getSavedTracks()
    {
        $token = $this->getToken();
        if ($token === false) {
            return null;
        }

            $response = $this->client->request('GET',
                'https://api.spotify.com/v1/me/tracks?limit=1', [
                    'headers' => [
                        'Authorization:' => 'Bearer ' . $token,
                        'Accept:' => 'application/json',
                        'Content-Type:' => 'application/json',
                    ]
                ]);

            $content = json_decode($response->getBody()->getContents(), true);

        return $content['total'];
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

    public function getListOfPlaylist()
    {
        $token = $this->getToken();
        if ($token === false) {
            return null;
        }

        $response = $this->client->request('GET',
                'https://api.spotify.com/v1/me/playlists?limit=1', [
                    'headers' => [
                        'Authorization:' => 'Bearer ' . $token,
                        'Accept:' => 'application/json',
                        'Content-Type:' => 'application/json',
                    ]
                ]);
            $content = json_decode($response->getBody()->getContents(), true);

        return $content['total'];
    }
}