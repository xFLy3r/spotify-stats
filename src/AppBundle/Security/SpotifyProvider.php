<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class SpotifyProvider extends OAuthUserProvider
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Doctrine
     */
    protected $doctrine;


        /***
         * @param Doctrine $doctrine
         * @param RequestStack $requestStack
         */
    public function __construct(Doctrine $doctrine, RequestStack $requestStack) {
        $this->doctrine = $doctrine;
        $this->request   = $requestStack->getCurrentRequest();
    }

    /***
     * @param UserResponseInterface $response
     * @return OAuthUser|\HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser|\Symfony\Component\Security\Core\User\UserInterface
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = $this->doctrine->getRepository('AppBundle:User')->findOneBy([
            'spotifyId' => $response->getUsername()]);
        if (!$user) {
            $user = new User($response->getUsername());
            $user->setSpotifyId($response->getUsername());
            $user->setName($response->getRealName());
            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();
            return $user;
        }

        return $this->loadUserByUsername($user->getId());
    }

    public function supportsClass($class)
    {
        return $class === 'HWI\\Bundle\\OAuthBundle\\Security\\Core\\User\\OAuthUser';
    }
}