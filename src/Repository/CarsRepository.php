<?php

namespace App\Repository;

use App\Entity\Cars;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
     * Fonction pour afficher toutes les marques
     *
     * @return void
     */
    public function findDistinctBrands()
    {
        return $this->createQueryBuilder('c')
            ->select('DISTINCT c.slugBrand,c.brand')
            ->orderby('c.brand',"asc")
            ->getQuery()
            ->getResult();
    }
    public function searchByKeyword(string $keyword)
    {
        $slugify = new Slugify();
        $slugKeyword = $slugify->slugify($keyword);
    
        return $this->createQueryBuilder('c')
            ->where('c.slug LIKE :slugKeyword')
            ->setParameter('slugKeyword', '%' . $slugKeyword . '%')
            ->getQuery()
            ->getResult();
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
