<?php

namespace AppBundle\Controller;


use AppBundle\Service\SpotifyRequester;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $spotify = $this->get('app.spotify_requester');
        return $this->render('@App/default/index.html.twig', [
            'user' => $spotify->getInfoAboutCurrentUser(),
            'artist' => $spotify->getFavouriteArtist(),
            'track' => $spotify->getFavouriteTrack(),
            'recentlyPlayed' => $spotify->getRecentlyPlayedTrack(),
            'countOfSavedTracks' => $spotify->getSavedTracks(),
            'favouriteGenre' => $spotify->getFavouriteGenre(),
            'countOfPlaylists' => $spotify->getListOfPlaylist(),
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
    * @Route("/statuses", name="statuses")
    */
    public function getStatusesAction()
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
        $statuses = [$countF, $countT];
        return new JsonResponse($statuses);
    }
}
