<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;

class UserRepository extends BaseRepository
{

    /**
     * @param array $emails
     * @return User[]
     */
    public function findByEmails(array $emails)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email IN (:emails)')
            ->setParameter('emails', $emails)
            ->getQuery()
            ->execute();
    }
}