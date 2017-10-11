<?php

namespace AppBundle\Controller;


use AppBundle\Service\SpotifyRequester;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $content = $this->get('app.spotify_requester')->getInfoAboutCurrentUser();
        dump($content);
        return $this->render('@App/default/index.html.twig', [
            'user' => $content,
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        return $this->render('default/login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {

    }
    /**
     * Route("/login/check-spotify", name="check-spotify")
     */
    //public function checkAction(Request $request)
    //{

    //}
}
