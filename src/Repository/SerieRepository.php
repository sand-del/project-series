<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function findBestSeries(int $page)
    {
//        //en DQL, on utilise les noms de nos attributs et non les noms de nos colonnes (même si ceux ci peuvent être identique)
//        $dql = "SELECT s FROM App\Entity\Serie AS s
//                WHERE s.popularity > 200
//                ORDER BY s.vote DESC";
//        //récupération de l'entityManager
//        $entityManager = $this->getEntityManager();
//        //création de la query
//        $query = $entityManager->createQuery($dql);
//        //set de la limite
//        $query->setMaxResults(10);
//        //retourne les résultats de la requête
//        return $query->getResult();

        //page = 1; 0 -> 19
        //page = 2; 20 -> 39
        $limit = Serie::SERIES_PER_PAGE;
        $offset = ($page - 1) * $limit;

        //La même requête en QueryBuilder
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->leftJoin('s.seasons', 'seas');
        $queryBuilder->addSelect('seas');

        $queryBuilder->addOrderBy('s.popularity', 'DESC');

        //pareil qu'en DQL
        $query = $queryBuilder->getQuery();
        //set de la limite
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);

        //ajout du paginatore pour gérer les différences de résultat du à la jointure
        $paginator = new Paginator($query);

        //retourne le résultat de la requête
        return $paginator;

    }

    //    /**
    //     * @return Serie[] Returns an array of Serie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Serie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
