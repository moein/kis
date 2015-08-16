<?php
/**
 * Created by PhpStorm.
 * User: moein_ak
 * Date: 15/08/15
 * Time: 19:03
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class BaseRepository extends EntityRepository
{
    public function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    public function save($entity)
    {
        $this->persist($entity);
        $this->getEntityManager()->flush($entity);
    }

    public function delete($entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush($entity);
    }
}