<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class SpotifyProvider extends OAuthUserProvider
{
    /**
     * @var Doctrine
     */
    protected $doctrine;

    /***
     * @param Doctrine $doctrine
     */
    public function __construct(Doctrine $doctrine) {
        $this->doctrine = $doctrine;
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
            $user = new User(
                $response->getUsername(),
                $response->getRealName(),
                $response->getResponse()['product']
            );

            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();
            return $user;
        }

        return $this->loadUserByUsername($user->getSpotifyId());
    }

    public function supportsClass($class)
    {
        return $class === 'HWI\\Bundle\\OAuthBundle\\Security\\Core\\User\\OAuthUser';
    }
}
