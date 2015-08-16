<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="share")
 */
class Share
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Document
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="shares")
     */
    private $document;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="shares")
     */
    private $user;

    /**
     * @var String
     * @ORM\Column(type="string", length=1000)
     */
    private $encryptedKey;

    public function __construct(Document $document, User$user, $encryptedKey)
    {
        $this->document = $document;
        $this->user = $user;
        $this->encryptedKey = $encryptedKey;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return String
     */
    public function getEncryptedKey()
    {
        return $this->encryptedKey;
    }
}