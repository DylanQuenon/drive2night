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
        $cars = $car->findLatestCars(3); //trouve les 3 dernières voitures avec le paramètre limit
        return $this->render('home.html.twig', [
            'controller_name' => 'HomeController',
            'cars'=>$cars//renvoie le tableau avec les dernières voitures
        ]);
    }
}
