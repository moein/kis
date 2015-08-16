<?php

namespace AppBundle\Entity;

use AppBundle\Encryption\KeyGen;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user", uniqueConstraints={@UniqueConstraint(name="username_idx", columns={"username"})})
 */
class User extends BaseUser
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The private key is only usable with the password provided by the user
     * This is the main point of this application
     *
     * @var String
     * @ORM\Column(type="string", length=5000)
     */
    private $privateKey;

    /**
     * @var String
     * @ORM\Column(type="string", length=5000)
     */
    private $publicKey;

    /**
     * @var Share[]
     * @ORM\OneToMany(targetEntity="Share", mappedBy="user")
     */
    private $shares;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return String
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param String $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return String
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param String $publicKey
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
    }

    /**
     * @return Share[]|ArrayCollection
     */
    public function getShares()
    {
        return $this->shares;
    }

    public function getDocuments()
    {
        return $this->getShares()->map(function(Share $share){
            return $share->getDocument();
        });
    }

    public function setPlainPassword($password)
    {
        $keyPair = KeyGen::generateKeyPair($password);
        $this->setPrivateKey($keyPair->privateKey);
        $this->setPublicKey($keyPair->publicKey);

        parent::setPlainPassword($password);
    }

    /**
     * @param Share[] $shares
     */
    public function setShares($shares)
    {
        $this->shares = $shares;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isEqual(User $user)
    {
        return $this->getId() == $user->getId();
    }
}