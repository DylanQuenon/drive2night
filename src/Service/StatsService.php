<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService{

    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersCount(): int
    {
        return $this->manager->createQuery("SELECT COUNT(u) FROM App\Entity\User u")->getSingleScalarResult(); 
    }

    public function getCarsCount(): int
    {
        return $this->manager->createQuery("SELECT COUNT(a) FROM App\Entity\Cars a")->getSingleScalarResult(); 
    }


    public function getCommentsCount(): int
    {
        return $this->manager->createQuery("SELECT COUNT(c) FROM App\Entity\Comment c")->getSingleScalarResult(); 
    }

    public function getCarsStat(string $direction): array
    {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
             FROM App\Entity\comment c
             JOIN c.cars a
             JOIN a.author u
             GROUP BY a
             ORDER BY note '.$direction
        )->setMaxResults(5)->getResult();
    }

}