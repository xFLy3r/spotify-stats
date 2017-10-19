<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Request;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SpotifyRequester
{
    CONST SPOTIFY_LIBRATY_URI = 'https://api.spotify.com/v1/me/';

    protected $client;

    protected $tokenStorage;

    protected $savedTracks;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->client = new Client();
    }

    private function refreshToken(OAuthToken $oAuthToken)
    {
            $date = new \DateTime();
            $oAuthToken->setCreatedAt($date->getTimestamp());
            $response = $this->client->request('POST', 'https://accounts.spotify.com/api/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $oAuthToken->getRefreshToken(),
                    'client_id' => 'c50aa5b6db424029a2760363aee0467f',
                    'client_secret' => '5776bef0a5bc46a89b8ff0a259d5d5c5',
            ]]);
            $mass = json_decode($response->getBody()->getContents(), true);
            $oAuthToken->setAccessToken($mass['access_token']);

            return $oAuthToken;
    }

    public function getInfoAboutCurrentUser()
    {
        $token = $this->getToken();
        if ($token === false) {
            return null;
        }
            $response = $this->client->get(SpotifyRequester::SPOTIFY_LIBRATY_URI, [
                'headers' => [
                    'Authorization:' => 'Bearer ' . $token,
                    'Accept:' => 'application/json',
                    'Content-Type:' => 'application/json',
                ]
            ]);

        return json_decode($response->getBody()->getContents(), true);
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

    public function getFavouriteGenreBySavedTracks()
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
        $myCount = (($content['total'] % 50)) ? intdiv($content['total'], 50) + 1: intdiv($content['total'], 50);

        $promises = [];
        $offset = 0;
        $tracks = [];
        for ($i = 0; $i < $myCount; $i++) {
            $promises[] = $this->client->requestAsync('GET', 'https://api.spotify.com/v1/me/tracks?limit=50&offset=' . $offset, [
                'headers' => [
                    'Authorization:' => 'Bearer ' . $token,
                    'Accept:' => 'application/json',
                    'Content-Type:' => 'application/json',
                ]
            ]);
            $offset += 50;
        };
        $promises = new EachPromise($promises, [
            'concurrency' => 5,
            'fulfilled' => function (ResponseInterface $responses) use (&$tracks) {
                $content = json_decode($responses->getBody()->getContents(), true);
                foreach ($content['items'] as $item) {
                    foreach ($item['track']['artists'] as $artist) {
                        $tracks[] = $artist['id'];
                    }
                }
                /**
                 * GOT LIST OF IDS ARTISTS $tracks
                 */
                $tracks = array_unique($tracks);
            },
        ]);
        $promises->promise()->wait();
        $offset = 0;
        $myCount = ((count($tracks) % 50)) ? intdiv(count($tracks), 50) + 1: intdiv(count($tracks), 50);
        $promises2 = [];
        for ($i = 0; $i < $myCount; $i++) {
            $string = '';
            $j = 0;
            foreach (array_slice($tracks, $offset) as  $id) {
                if ($j == 50) break;
                $string .= $id . ',';
                $j++;
            }
            $newString = substr($string, 0, -1);
            $promises2[] = $this->client->requestAsync('GET', 'https://api.spotify.com/v1/artists?ids=' . $newString, [
                'headers' => [
                    'Authorization:' => 'Bearer ' . $token,
                    'Accept:' => 'application/json',
                    'Content-Type:' => 'application/json',
                ]
            ]);
            $offset += 50;
        }
        $genres = [];
        $promises2 = new EachPromise($promises2, [
            'concurrency' => 5,
            'fulfilled' => function (ResponseInterface $responses) use (&$genres) {
                $content = json_decode($responses->getBody()->getContents(), true);
                foreach ($content as $item) {
                    foreach ($item as $artist) {
                        foreach ($artist['genres'] as $genre) {
                            $genres[] = $genre;
                        }
                    }
                }
                /**
                 * GOT LIST OF IDS ARTISTS $tracks
                 */

            },
        ]);
        $promises2->promise()->wait();

        return ucwords(strtolower(array_search(max(array_count_values($genres)), array_count_values($genres))));
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
            if ($oAuthToken->isExpired()) {
                return $this->refreshToken($oAuthToken);
            }

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
