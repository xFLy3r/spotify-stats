<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    CONST PREMIUM = 'premium';
    CONST FREE = 'free';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $srcImage;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_premium", type="boolean")
     */
    private $isPremium;

    public function __construct($username, $realName, $productType, $srcImage, $email)
    {
        $this->isActive = true;
        $this->setUsername($username);
        $this->setName($realName);
        $this->setIsPremium($productType);
        $this->setSrcImage($srcImage);
        $this->setEmail($email);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return null;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
        return null;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isPremium
     *
     * @param boolean $isPremium
     *
     * @return User
     */
    public function setIsPremium($isPremium)
    {
        if (self::PREMIUM === $isPremium) {
            $this->isPremium = true;
        } else{
            $this->isPremium = false;
        }

        return $this;
    }

    /**
     * Get isPremium
     *
     * @return boolean
     */
    public function getIsPremium()
    {
        return $this->isPremium;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set srcImage
     *
     * @param string $srcImage
     *
     * @return User
     */
    public function setSrcImage($srcImage)
    {
        $this->srcImage = $srcImage;

        return $this;
    }

    /**
     * Get srcImage
     *
     * @return string
     */
    public function getSrcImage()
    {
        return $this->srcImage;
    }
}
