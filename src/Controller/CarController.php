<?php

namespace App\Controller;

use App\Entity\Cars;
use App\Repository\CarsRepository;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarController extends AbstractController
{

    #[Route('/cars/{page<\d+>?1}', name: 'cars_index')]
    public function index(CarsRepository $repo,$page,PaginationService $pagination): Response
    {
        $pagination->setEntityClass(Cars::class)
                    ->setPage($page)
                    ->setLimit(9);
        return $this->render('car/index.html.twig', [
            'pagination' => $pagination
        ]);
        
    }
}
