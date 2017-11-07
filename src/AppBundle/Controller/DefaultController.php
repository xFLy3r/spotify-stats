<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $spotify = $this->get('app.spotify_requester');
        return $this->render('@App/default/index.html.twig', [
            'track' => $spotify->getFavouriteTrack(),
            'countOfSavedTracks' => $spotify->getSavedTracks(),
            'countOfPlaylists' => $spotify->getCountOfPlaylist(),
        ]);
    }

    /**
     * Route("/login", name="login")
     */
    public function loginAction()
    {
        return $this->render('@App/default/login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {

    }

    /**
     * @Route("/favourite/genre", name="favourite_genre")
     *
     */
    public function getFavouriteGenre()
    {
        return new JsonResponse($this->get('app.spotify_requester')->getFavouriteGenre());
    }

    /**
     * @Route("/favourite/artist", name="favourite_artist")
     *
     */
    public function getFavouriteArtist()
    {
        return JsonResponse::fromJsonString($this->get('app.spotify_requester')->getFavouriteArtist());
    }

    /**
     * @Route("/favourite/track", name="favourite_track")
     *
     */
    public function getFavouriteTrack()
    {
        return JsonResponse::fromJsonString($this->get('app.spotify_requester')->getFavouriteTrack());
    }

    /**
     * @Route("/recently/track", name="recently_track")
     *
     */
    public function getRecenltyPlayedTrack()
    {
        return JsonResponse::fromJsonString($this->get('app.spotify_requester')->getRecentlyPlayedTrack());
    }
   /**
    * @Route("/statuses", name="statuses")
    */
    public function getProductsAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $countF = 0;
        $countT = 0;
        foreach ($users as $user) {
            if ($user->getIsPremium()) {
                $countT++;
            } else {
                $countF++;
            }
        }
        return new JsonResponse(array(
            'countOfPremium' => $countT,
            'countOfFree' => $countF,
        ));
    }
}
