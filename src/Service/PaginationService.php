<?php
namespace App\Service;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class PaginationService{
    private $entityClass;
    private $limit = 9;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request){
        $this->manager = $manager;
        $this->twig = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
    }
    public function setRoute($route){
        $this->route = $route;
        return $this;
    }
    public function getRoute(){
        return $this->route;
    }

    public function display(){
        $this->twig->display('car/pagination.html.twig',[
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }
    public function getData(){
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        // Demander au repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([],[],$this->limit, $offset);
        // renvoyer les éléments en question
        return $data;
    }
    public function getPages(){
        //Connaitre le total des enregistrament de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        // faire la division, l'arrondi et le renvoyer
        $pages = ceil($total/$this->limit);
        return $pages;
    }
    public function setPage($page){
        $this->currentPage = $page;
        return $this;
    }
    public function getPage(){
        return $this->currentPage;
    }
    public function setLimit($limit){
        $this->limit = $limit;
        return $this;
    }
    public function getLimit(){
        return $this->limit;
    }
    public function setEntityClass($entityClass){
        $this->entityClass = $entityClass;
        return $this;
    }
    public function getEntityClass(){
        return $this->entityClass;
    }
}

?>