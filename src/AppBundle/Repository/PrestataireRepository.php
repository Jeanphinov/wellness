<?php

namespace AppBundle\Repository;

/**
 * PrestataireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PrestataireRepository extends \Doctrine\ORM\EntityRepository
{
    public function findNames(){
        $em=$this->getEntityManager();
        $noms=$em->createQuery('SELECT p.id, p.nom FROM AppBundle:Prestataire p');
        return $noms->getResult();
    }
}