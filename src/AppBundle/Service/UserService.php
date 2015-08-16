<?php

namespace AppBundle\Service;

use AppBundle\Encryption\KeyGen;
use AppBundle\Entity\Document;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;

class UserService
{
    /** @var UserRepository */
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUnsharedUsers(Document $document)
    {
        /** @var User[] $users */
        $users = $this->userRepository->findAll();

        $unsharedUsers = [];
        foreach ($users as $user)
        {
            if ($document->getShareOf($user) === null)
            {
                $unsharedUsers[] = $user;
            }
        }

        return $unsharedUsers;
    }

    /**
     * @param array $emails
     * @return User[]
     */
    public function getUsersByEmails(array $emails)
    {
        if (count($emails) === 0)
        {
            return [];
        }
        return $this->userRepository->findByEmails($emails);
    }

    public function getSharedUsers(Document $document)
    {
        $sharedUsers = [];
        foreach ($document->getShares() as $share)
        {
            if (!$share->getUser()->isEqual($document->getOwner()))
            {
                $sharedUsers[] = $share->getUser();
            }
        }

        return $sharedUsers;
    }
}