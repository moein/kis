<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DocumentRepository")
 * @ORM\Table(name="document")
 */
class Document
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @var resource
     * @ORM\Column(type="blob")
     */
    private $encryptedContent;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $owner;

    /**
     * @var Share[]
     * @ORM\OneToMany(targetEntity="Share", mappedBy="document")
     */
    private $shares;

    public function __construct(User $creator, $encryptedKey)
    {
        $this->owner = $creator;
        $this->shareWith($creator, $encryptedKey);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Share[]
     */
    public function getShares()
    {
        return $this->shares;
    }

    /**
     * @param Share[] $shares
     */
    public function setShares($shares)
    {
        $this->shares = $shares;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return String
     */
    public function getEncryptedContent()
    {
        return stream_get_contents($this->encryptedContent);
    }

    /**
     * @param String $encryptedContent
     */
    public function setEncryptedContent($encryptedContent)
    {
        $this->encryptedContent = $encryptedContent;
    }

    /**
     * @param User $user
     * @return Share
     */
    public function getShareOf(User $user)
    {
        foreach ($this->getShares() as $share)
        {
            if ($share->getUser()->isEqual($user))
            {
                return $share;
            }
        }

        return null;
    }

    public function removeShareOf(User $user)
    {
        foreach ($this->getShares() as $key => $share)
        {
            if ($share->getUser()->isEqual($user))
            {
                unset($this->shares[$key]);
                break;
            }
        }
    }

    /**
     * @param User $shareWith
     * @param String $encryptedKey
     * @return Share
     */
    public function shareWith(User $shareWith, $encryptedKey)
    {
        $share = new Share($this, $shareWith, $encryptedKey);
        $this->shares[] = $share;

        return $share;
    }

    public function getOwner()
    {
        return $this->owner;
    }
}