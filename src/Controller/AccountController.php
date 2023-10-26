<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;

class AccountController extends AbstractController
{
    /**
     * Permet de se connecter
     *
     * @return Response
     */
    #[Route('/login', name: 'account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        $loginError = null;

        //dump($error);

        if($error instanceof TooManyLoginAttemptsAuthenticationException)
        {
            // l'ereur est due à la limitation de tentative de connexion
            $loginError = "Trop de tentatives de connexion. Réessayez plus tard";
        }




        return $this->render('account/index.html.twig', [
            
            'hasError' => $error !== null,
            'username' => $username,
            'loginError' => $loginError
        ]);
    }
    /**
     * Permet de se déconnecter
     *
     * @return void
     */
    #[Route("/logout", name: "account_logout")]
    public function logout(): void
    {

    }
     /**
     * Permet d'afficher le formulaire d'inscription ainsi la gestion de l'inscription de l'utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route("/register", name:"account_register")]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        // partie traitement du formulaire
        if($form->isSubmitted() && $form->isValid())
        {
            // gestion de l'inscription dans la bdd
            $hash = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();


            return $this->redirectToRoute('account_login');
        }


        return $this->render("account/registration.html.twig",[
            'myForm' => $form->createView()
        ]);
    }
     /**
     * Permet de modifier l'utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/profile", name:"account_profile")]
    public function profile(Request $request, EntityManagerInterface $manager): Response
    {
        // Vérifie si un utilisateur est connecté
        if ($this->getUser()) {
            $user = $this->getUser(); // permet de récupérer l'utilisateur connecté
            $form = $this->createForm(AccountType::class, $user);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    "Les données ont été enregistrées avec succès"
                );
            }
    
            return $this->render("account/profile.html.twig", [
                'myForm' => $form->createView()
            ]);
        } else {
            // Redirige l'utilisateur vers la page de connexion ou affiche un message d'erreur
            $this->addFlash('warning', "Vous devez être connecté pour accéder à votre profil.");
            return $this->redirectToRoute('account_login'); // Redirige vers la page de connexion
        }
    }
}
