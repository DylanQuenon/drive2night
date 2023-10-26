<?php

namespace App\Controller;

use DateTime;
use App\Entity\Cars;
use App\Entity\Image;
use App\Form\CarType;
use DateTimeImmuable;
use App\Entity\Comment;
use App\Form\CommentType;
use Cocur\Slugify\Slugify;
use App\Repository\CarsRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarController extends AbstractController
{
    /**
     * Permet de créer une voiture 
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/cars/new", name:"cars_create")]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

    if (!$user) {
        $this->addFlash('warning', "Vous devez être connecté pour créer une annonce.");
        return $this->redirectToRoute('account_login'); // Redirige vers la page de connexion
    }
        $car= new Cars();
        $form = $this->createForm(CarType::class, $car);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($car->getImages() as $image)
            {
                $image->setCar($car);
                $manager->persist($image);
            }
            $car->setAuthor($this->getUser());

            // je persiste mon objet Ad
            $manager->persist($car);
            // j'envoie les persistances dans la bdd
            $manager->flush();

            $this->addFlash('success', "L'annonce <strong>".$car->getBrand().$car->getModel()."</strong> a bien été enregistrée");

            return $this->redirectToRoute('cars_show',[
                'slug' => $car->getSlug(),
              
            ]);

        }
        return $this->render("car/new.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Fonction permettant d'afficher les résultats de la recherche
     *
     * @param Request $request
     * @param CarsRepository $carsRepository
     * @return Response
     */
    #[Route("/cars/search", name: "cars_search")]
    public function search(Request $request, CarsRepository $carsRepository): Response
    {
        $query = $request->query->get('q'); //récupère la recherche du repo

        if ($query) {
            $results = $carsRepository->searchByKeyword($query);  //si il y'a une recherche tu recherches sinon tu laisses le tableau vide
        } else {
            $results = [];
          
        }

        return $this->render('car/search.html.twig', [
            'query' => $query,
            'results' => $results,
         
        ]);
    }
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
    public function brandCars(string $slugBrand, CarsRepository $carsRepository,): Response
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
    public function show(Request $request, string $slug, Cars $car, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCar($car)
                ->setAuthor($this->getUser());
        
            $manager->persist($comment);
            $manager->flush();
        
            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte'
            );
        
            // Rediriger vers la même page pour réinitialiser le formulaire
            return $this->redirectToRoute('cars_show', ['slug' => $car->getSlug()]);
        }
    
        return $this->render("car/show.html.twig", [
            'car' => $car,
            'myForm' => $form->createView()
        ]);
    }
    
    /**
     * Permet d'éditier une annonce
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Ad $ad
     * @return Response
     */
    #[Route("/cars/{slug}/edit", name:"cars_edit")]
    public function edit(Request $request, EntityManagerInterface $manager, Cars $car): Response
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);
         if ($this->getUser() !== $car->getAuthor()) {
        // Redirige l'utilisateur vers une page d'erreur ou affiche un message d'erreur
        $this->addFlash('warning', "Vous n'avez pas la permission de modifier cette annonce.");
        return $this->redirectToRoute('cars_show', ['slug' => $car->getSlug()]);
    }


        if($form->isSubmitted() && $form->isValid())
        {
            //je reslugify la marque au cas où elle change
            $newBrand = $car->getBrand();
            $slugify = new Slugify();
            $newSlugBrand = $slugify->slugify($newBrand);
            $carSlug = $newSlugBrand .' '.$car->getModel();
            $newCarSlug = $slugify->slugify($carSlug); 
            $car->setSlug($newCarSlug);
            $car->setSlugBrand($newSlugBrand);
            
            foreach($car->getImages() as $image)
            {
                $image->setCar($car);
                $manager->persist($image);
            }

            $manager->persist($car);
            $manager->flush();

            $this->addFlash(
            'success',
            "La voiture <strong>".$car->getBrand().$car->getModel()."</strong> a bien été modifiée!"
            );

            return $this->redirectToRoute('cars_show',[
            'slug' => $car->getSlug()
            ]);

        }

        return $this->render("car/edit.html.twig", [
            "car" => $car,
            "myForm" => $form->createView()
        ]);
    }

    /**
     * Permet d'effacer une voiture
     *
     * @param Cars $car
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/cars/{slug}/delete", name:"cars_delete")]
    public function delete(Cars $car, EntityManagerInterface $manager): Response
    {
        $this->addFlash(
            'success',
            "La voiture <strong>".$car->getBrand().$car->getModel()."</strong> a bien été supprimée"
        );

        $manager->remove($car);
        $manager->flush();

        return $this->redirectToRoute('cars_index');
    }


}
