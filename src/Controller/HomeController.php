<?php

namespace App\Controller;

use App\Repository\CarsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Affiche la page home
     *
     * @param CarsRepository $car
     * @return Response
     */
    #[Route('/', name: 'homepage')]
    public function index(CarsRepository $car): Response
    {
        $latestCars = $car->findBy([], ['id' => 'DESC'], 3); // Trouve les 3 dernières voitures
    
        return $this->render('home.html.twig', [
            'controller_name' => 'HomeController',
            'cars' => $latestCars,
        ]);
    }
}
