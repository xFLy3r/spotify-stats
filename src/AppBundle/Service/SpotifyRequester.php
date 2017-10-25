<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SpotifyRequester
{
    CONST SPOTIFY_LIBRARY_URI = 'https://api.spotify.com/v1/me/';
    CONST CLIENT_ID = 'c50aa5b6db424029a2760363aee0467f';
    CONST CLIENT_SECRET = '5776bef0a5bc46a89b8ff0a259d5d5c5';

    protected $client;

    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->client = new Client();
    }

    public function getInfoAboutCurrentUser(): ?array
    {
        if (!$token = $this->getToken()) {
           return null;
        }

        $response = $this->client->get(self::SPOTIFY_LIBRARY_URI, [
            'headers' => [
                'Authorization:' => 'Bearer ' . $token,
                'Accept:' => 'application/json',
                'Content-Type:' => 'application/json'
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getFavouriteArtist(): ?array
    {
        if (!$token = $this->getToken()) {
            return null;
        }

        $response = $this->client->get(self::SPOTIFY_LIBRARY_URI . 'top/artists?limit=1&time_range=long_term', [
            'headers' => [
                'Authorization:' => 'Bearer ' . $token,
                'Accept:' => 'application/json',
                'Content-Type:' => 'application/json'
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getFavouriteTrack(): ?array
    {
        if (!$token = $this->getToken()) {
            return null;
        }

        $response = $this->client->get(self::SPOTIFY_LIBRARY_URI . 'top/tracks?limit=1&time_range=long_term', [
            'headers' => [
                'Authorization:' => 'Bearer ' . $token,
                'Accept:' => 'application/json',
                'Content-Type:' => 'application/json'
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getRecentlyPlayedTrack(): ?array
    {
        if (!$token = $this->getToken()) {
            return null;
        }

        $response = $this->client->get(self::SPOTIFY_LIBRARY_URI . 'player/recently-played?limit=1', [
            'headers' => [
                'Authorization:' => 'Bearer ' . $token,
                'Accept:' => 'application/json',
                'Content-Type:' => 'application/json'
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getFavouriteGenre(): ?string
    {
        if (!$token = $this->getToken()) {
            return null;
        }

        $response = $this->client->get(self::SPOTIFY_LIBRARY_URI . 'top/artists?limit=50&time_range=long_term', [
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

    public function getSavedTracks(): ?int
    {
        if (!$token = $this->getToken()) {
            return null;
        }

        $response = $this->client->get(self::SPOTIFY_LIBRARY_URI . 'tracks?limit=1', [
            'headers' => [
                'Authorization:' => 'Bearer ' . $token,
                'Accept:' => 'application/json',
                'Content-Type:' => 'application/json',
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true)['total'];
    }

    public function getCountOfPlaylist(): ?int
    {
        if (!$token = $this->getToken()) {
            return null;
        }

        $response = $this->client->get(self::SPOTIFY_LIBRARY_URI . 'playlists?limit=1', [
            'headers' => [
                'Authorization:' => 'Bearer ' . $token,
                'Accept:' => 'application/json',
                'Content-Type:' => 'application/json',
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true)['total'];
    }

    protected function getToken()
    {
        if ($this->tokenStorage->getToken() instanceof OAuthToken) {
            /** @var OAuthToken $oAuthToken */
            $oAuthToken = $this->tokenStorage->getToken();
            if ($oAuthToken->isExpired()) {
                return $this->refreshToken($oAuthToken)->getAccessToken();
            }

            return $oAuthToken->getAccessToken();
        }

        return false;
    }

    private function refreshToken(OAuthToken $oAuthToken): OAuthToken
    {
        $date = new \DateTime();
        $oAuthToken->setCreatedAt($date->getTimestamp());
        $response = $this->client->post('https://accounts.spotify.com/api/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $oAuthToken->getRefreshToken(),
                'client_id' => self::CLIENT_ID,
                'client_secret' => self::CLIENT_SECRET,
            ]
        ]);
        $arrayResponse = json_decode($response->getBody()->getContents(), true);
        $oAuthToken->setAccessToken($arrayResponse['access_token']);

        return $oAuthToken;
    }
}
