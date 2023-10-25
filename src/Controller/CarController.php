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
    /**
     * Affiche toutes les marques 
     *
     * @param CarsRepository $carsRepository
     * @return Response
     */
    #[Route('/cars/brands', name: 'cars_brands_list')]
    public function brandList( CarsRepository $carsRepository): Response
    {
        $brands = $carsRepository->MarquesAutorisees(); 

        return $this->render('car/brands_list.html.twig', [
            'brands' => $brands,
        ]);
    }
    /**
     * Affiche les voitures relative à la marque
     *
     * @param string $slugBrand
     * @param CarsRepository $carsRepository
     * @return Response
     */
    #[Route('/cars/brands/{slugBrand}', name: 'cars_brand')]
    public function brandCars(string $slugBrand, CarsRepository $carsRepository): Response
    {
        //remplace le string par des tirets pour obtenir le nom de la marque
        $brand = str_replace('-', ' ', $slugBrand);
        
        // Récupère les voitures associées au nom de la marque
        $cars = $carsRepository->findBy(['brand' => $brand]);
        
        return $this->render('car/brands.html.twig', [
            'brand' => $brand, //récupère la marque
            'cars' => $cars,
        ]);
    }

    
    /**
     * Sert à afficher tous les produits avec une pagination
     *
     * @param CarsRepository $repo
     * @param [type] $page
     * @param PaginationService $pagination
     * @return Response
     */
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
    /**
     * Affiche les voitures individuellement
     *
     * @param string $slug
     * @param Cars $car
     * @return Response
     */
    #[Route("/cars/{slug}", name:"cars_show")]
    public function show(string $slug, Cars $car): Response
    {

        return $this->render("car/show.html.twig", [
            'car' => $car
        ]);
    }
}
