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
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
       
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

            $this->addFlash('success', "The car <strong>".$car->getFullCar()."</strong> has been successfully saved");

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
        $cars = $carsRepository->findBy(['brand' => $brand]); //permet de sélectionner les voitures à la marque 
        
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

     //regex pour la pagination, il va prendre le paramètre page qui doit contenir un chiffre entre 0 et 9, si il n'ya rien la valeur par défaut sera 1
    #[Route('/cars/{page<\d+>?1}', name: 'cars_index')] 
    public function index(CarsRepository $repo,$page,PaginationService $pagination): Response
    {
        
      
        $pagination->setEntityClass(Cars::class)//Définit l'entité pour la pagination
                    ->setPage($page)//calculer le nombre de page (paramètre renvoyé dans l'url)
                    ->setLimit(9);//la limite par page sera de 9
        return $this->render('car/index.html.twig', [
            'pagination' => $pagination //on renvoie les éléments à paginer
        ]);
        
    }
    
    /**
     * Récupère la voiture individuelle et va afficher et traiter le form pour ajouter un commentaire
     *
     * @param Request $request
     * @param string $slug
     * @param Cars $car
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/cars/{slug}", name:"cars_show")]
    public function show(Request $request, string $slug, Cars $car, EntityManagerInterface $manager): Response
    {
        $comment = new Comment(); //créé un nouveau commentaire
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie si l'utilisateur essaie de commenter sa propre voiture
            if ($this->getUser() === $car->getAuthor()) {
                $this->addFlash(
                    'warning',
                    'You cannot comment on your own car.'
                );
                return $this->redirectToRoute('cars_show', ['slug' => $car->getSlug()]);
            }
    
            // Vérifie s'il existe déjà un commentaire pour cette voiture par cet utilisateur
            $existingComment = $manager->getRepository(Comment::class)->findOneBy([
                'car' => $car,
                'author' => $this->getUser(),
            ]);
    
            if ($existingComment) {
                $this->addFlash(
                    'warning',
                    'You have already commented on this car. Only one comment per car is allowed.'
                );
                return $this->redirectToRoute('cars_show', ['slug' => $car->getSlug()]);
            }
    
            $comment->setCar($car) //récupère la voiture commentée
                    ->setAuthor($this->getUser());//récupère l'auteur qui a commenté
    
            // Persister le commentaire
            $manager->persist($comment);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "Your comment has been successfully recorded."
            );
    
            // Rediriger vers la même page pour réinitialiser le formulaire
            return $this->redirectToRoute('cars_show', ['slug' => $car->getSlug()]);
        }
    
        return $this->render("car/show.html.twig", [
            'car' => $car, //récupère la voiture en question
            'myForm' => $form->createView() //récup le formulaire pour les commentaires
        ]);
    }
    
   
    /**
     * Modifier une voiture
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Cars $car
     * @return Response
     */
    #[Route("/cars/{slug}/edit", name:"cars_edit")]
    #[IsGranted(
        attribute: new Expression('(user === subject and is_granted("ROLE_USER")) or is_granted("ROLE_ADMIN")'),
        subject: new Expression('args["car"].getAuthor()'),
        message: "This car does not belong to you; you cannot modify it."
    )]
    public function edit(Request $request, EntityManagerInterface $manager, Cars $car): Response
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);
        


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
            "The car <strong>".$car->getFullCar()."</strong> has been modified!"
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
    #[IsGranted(
        attribute: new Expression('(user === subject and is_granted("ROLE_USER")) or is_granted("ROLE_ADMIN")'),
        subject: new Expression('args["car"].getAuthor()'),
        message: "This car doesn't belong to you, you can't delete it."
    )]
    public function delete(Cars $car, EntityManagerInterface $manager): Response
    {
        $this->addFlash(
            'success',
            "The car <strong>".$car->getBrand().$car->getModel()."</strong> has been deleted."
        );

        $manager->remove($car);
        $manager->flush();

        return $this->redirectToRoute('cars_index');
    }
    #[Route("/comment/{id}/delete", name: "comment_delete")]
    #[IsGranted(
        attribute: new Expression('(user === subject and is_granted("ROLE_USER")) or is_granted("ROLE_ADMIN")'),
        subject: new Expression('args["comment"].getAuthor()'),
        message: "This comment does not belong to you, you cannot delete it."
    )]
    public function deleteComment(Comment $comment, EntityManagerInterface $manager): Response
    {
        // Vérifier si l'utilisateur connecté est l'auteur du commentaire
      
        $manager->remove($comment);
        $manager->flush();
    
        $this->addFlash('success', "The comment has been successfully deleted.");

        return $this->redirectToRoute('account_index');
    }

}
