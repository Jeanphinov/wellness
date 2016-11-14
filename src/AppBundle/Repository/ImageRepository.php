<?php

namespace AppBundle\Repository;

/**
 * ImageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ImageRepository extends \Doctrine\ORM\EntityRepository
{
    public function findFrontImages($nbre = 5)
    {

        $em = $this->getEntityManager();
        /*$images = $em->createQuery("SELECT i FROM AppBundle:Image i where i.imageType='photo' ")
            ->setMaxResults($nbre);
        return $images->getResult();*/
        $qb = $em->createQueryBuilder();
        $images = $qb->select('i')
            ->from($this->_entityName, 'i')
            ->where('i.prestatairePhotos IS NOT NULL')
            ->orderBy('i.id', 'desc')
            ->setMaxResults($nbre);
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;


    }
}
