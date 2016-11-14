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
    public function myFindAll($max = null)
    {
        $qb = $this->createQueryBuilder('p');

        $this->selectPrestataireLogosCategories($qb);
        $qb->addOrderBy('p.nom', 'ASC')
            ->setMaxResults($max);
        return $qb->getQuery()->execute();
    }

    /**
     * @param null $max
     * @return Paginator
     * findLastN retourne les n élément // $max représente le nombre max d'élement à retourner par le queryBuilder
     */
    public function findLastN($max = null)
    {
        $qb = $this->createQueryBuilder('p');

        $this->selectPrestataireLogosCategories($qb, $max);
        $qb->addOrderBy('p.id', 'DESC')
            ->setMaxResults($max);

        return new paginator($qb);
        // utilisation de paginator pour la limitation du nombre de résultats
        // sinon setMaxResults ne compte pas le nombre d'objets à la sortie mais le nombre d'entité dans la requete
        // pour $max=4 le queryBuilde ne renvoie que un objet.

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

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param null $max
     * @return \Doctrine\ORM\QueryBuilder
     * Methode qui sélectionne tous les prestataire, les logos et categories
     * recois le queryBuilder et le renvoie // evite de dupliquer du code dans mon repository
     */
    private function selectPrestataireLogosCategories(\Doctrine\ORM\QueryBuilder $qb, $max = null)
    {

        return $qb
            ->leftJoin('p.categories', 'c')
            ->addSelect('c')
            ->leftJoin('p.logos', 'l')
            ->addSelect('l');


    }

}
