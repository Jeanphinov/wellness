<?php

namespace AppBundle\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * PrestataireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PrestataireRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * getList
     * ne récupère que le minimum d'infos nécessaire pour un minimum de requetes
     * si j'appelle cette methode c'est que je n'ai besoin que d'une liste
     */
    public function getList($max = null)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.slug, p.nom, p.dateInscription')
            ->orderBy('p.dateInscription', 'DESC')
            ->setMaxResults($max);

        return $qb->getQuery()->execute();

    }


    /**
     * @param null $max
     * @return mixed
     * myFindAll
     * j'utilise le querybuilder pour récupérer les prestataires
     * je récupere egalement les logos et les categories liées
     */
    public function myFindAll($page = 1, $n = 10)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.localite', 'loc')
            ->leftJoin('p.logos', 'logos')
            ->leftJoin('p.photos', 'photos')
            ->leftJoin('loc.commune', 'commune')
            ->leftJoin('loc.codePostal', 'cPostal')
            ->addSelect('p')
            // ->addSelect('p.id id, p.slug, p.dateInscription, p.nom, p.tel, p.email, p.siteWeb ')
            ->addSelect('c, loc, commune, cPostal')
            ->addSelect('logos', 'photos');

        //Je prépare la pagination
        $qb->setFirstResult(($page - 1) * $n)
            ->setMaxResults($n);

        //return $qb->getQuery()->getResult();
        return new Paginator($qb);
    }

    /**
     * @return array
     * Compte le nombre de prestataires dans la bd
     * getScalarResult() renvoie un seul nombre
     * tout est dans le return car je ne fais qu'une seule chose c'est plus simple
     */
    public function countPrestataires(){
        return $this->createQueryBuilder('p')
            ->select('count(p)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param null $max
     * @return Paginator
     * findLastN retourne les n élément // $max représente le nombre max d'élement à retourner par le queryBuilder
     */
    public function findLastN($max = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.id, p.nom, p.tel, p.slug, p.dateInscription')
            ->leftJoin('p.localite', 'local')
            ->addSelect('local.localite')
            ->leftJoin('local.commune', 'comm')
            ->addSelect('comm.commune')
            ->leftJoin('p.logos', 'logos')
            ->addSelect('logos.url')
            ->addOrderBy('p.dateInscription', 'DESC')
            ->setMaxResults($max);

        return $qb->getQuery()->execute();

    }

    public function searchPrestataire($prestataire = null, $localite = null, $service = null)
    {


        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.logos', 'lg')
            ->leftJoin('p.localite', 'l')
            ->leftJoin('l.commune', 'com')
            ->leftJoin('p.categories', 'c')
            ->addSelect('lg')
            ->addSelect('p')
            ->addSelect('l')
            ->addSelect('com')
            ->addSelect('c');

        if (!empty ($prestataire)) {

            $qb->andwhere("p.nom LIKE :nom")
                ->setParameter('nom', "%" . $prestataire . "%");
        }

        if (!empty($localite)) {
            $qb->andWhere('l.localite like :localite')
                ->setParameter('localite', "%" . $localite . "%");
        }

        if (!empty($service)) {
            $qb->andWhere('c.nom like :categorie')
                ->setParameter('categorie', "%" . $service . "%");
        }

        return new Paginator($qb);
    }

    public function getFavoris($user, $n=null)
    {

        $qb = $this->createQueryBuilder('p')
            ->join('p.favoris', 'fav')
            ->join('p.logos', 'logos')
            ->join('p.localite', 'loc')
            ->select('p, loc, logos')
            ->andWhere('fav.id = :uid')
            ->setParameter('uid', $user->getId())
            ->setMaxResults($n)
        ;


        return $qb->getQuery()->getResult();
    }

}
