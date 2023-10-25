<?php

namespace App\Repository;

use App\Entity\Cars;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cars>
 *
 * @method Cars|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cars|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cars[]    findAll()
 * @method Cars[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cars::class);
    }
    /**
     * Permet de récupérer les dernières voitures enregistrées
     *
     * @param [type] $limit
     * @return void
     */
    public function findLatestCars($limit)
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC') 
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    /**
     * Fonction pour afficher toutes les marques
     *
     * @return void
     */
    public function MarquesAutorisees()
    {
        $qb = $this->createQueryBuilder('c')
        ->orderBy('c.brand', 'ASC')
        ->select('DISTINCT c.slugBrand,c.brand')
        ->getQuery();

        $results = $qb->getResult();

        $marquesAutorisees = [];
        foreach ($results as $result) {
            $marquesAutorisees[] = [
                'slugBrand' => $result['slugBrand'],
                'brand' => $result['brand']
            ];
        }

        return $marquesAutorisees;
    }

//    /**
//     * @return Cars[] Returns an array of Cars objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cars
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
