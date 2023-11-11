<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\ImgModifyType;
use App\Entity\UserImgModify;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
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
            // l'erreur est due à la limitation de tentative de connexion
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
            // gestion de l'image
            $file = $form['picture']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);

            }
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
    #[IsGranted('ROLE_USER')]
    public function profile(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        // Récupère le nom du fichier actuel
        $fileName = $user->getPicture();

        // Crée le formulaire
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si une nouvelle image est téléchargée, traitez-la
            $uploadedFile = $form->get('picture')->getData();

            if ($uploadedFile) {
                // Génère un nom de fichier unique
                $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

                // Déplace l'image téléchargée vers le répertoire d'uploads
                $uploadedFile->move(
                    $this->getParameter('uploads_directory'),
                    $fileName
                );
            }

            // Mise à jour d'autres informations de l'utilisateur
            $user->setSlug('');

            // Mise à jour du nom du fichier dans l'entité
            $user->setPicture($fileName);

            // Persiste et flush dans la base de données
            $manager->persist($user);
            $manager->flush();

            // Flash message de succès
            $this->addFlash(
                'success',
                "Data successfully recorded."
            );
        }

        // Rendu du template
        return $this->render("account/profile.html.twig", [
            'myForm' => $form->createView()
        ]);
    }
        /**
     * Permet de modifier le mot de passe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route("/account/password-update", name:"account_password")]
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // vérifier si le mot de passe correspond à l'ancien
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getPassword()))
            {
                // gestion de l'erreur
                $form->get('oldPassword')->addError(new FormError("The password you entered is not your current password"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Your password has been changed.'
                );

                return $this->redirectToRoute('homepage');
            }

        }

        return $this->render("account/password.html.twig", [
            'myForm' => $form->createView()
        ]);

    }
     /**
     * Permet de supprimer l'image de l'utilisateur
     *
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route("/account/delimg", name:"account_delimg")]
    public function removeImg(EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if(!empty($user->getPicture()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$user->getPicture());
            $user->setPicture('');
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Your avatar has been deleted.'
            );
        }

        return $this->redirectToRoute('homepage');

    }

    /**
     * Permet de modifier l'avatar de l'utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route("/account/imgmodify", name:"account_modifimg")]
    public function imgModify(Request $request, EntityManagerInterface $manager): Response
    {
        $imgModify = new UserImgModify();
        $user = $this->getUser();
        $form = $this->createForm(ImgModifyType::class, $imgModify);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //permet de supprimer l'image dans le dossier
            // gestion de la non-obligation de l'image
            if(!empty($user->getPicture()))
            {
                unlink($this->getParameter('uploads_directory').'/'.$user->getPicture());
            }

              // gestion de l'image
              $file = $form['newPicture']->getData();
              if(!empty($file))
              {
                  $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                  $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                  $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                  try{
                      $file->move(
                          $this->getParameter('uploads_directory'),
                          $newFilename
                      );
                  }catch(FileException $e)
                  {
                      return $e->getMessage();
                  }
                  $user->setPicture($newFilename);
              }
              $manager->persist($user);
              $manager->flush();

              $this->addFlash(
                'success',
                'Your avatar has been modified.'
              );

              return $this->redirectToRoute('homepage');

        }

        return $this->render("account/imgModify.html.twig",[
            'myForm' => $form->createView()
        ]);
    }
   
    
    

}
