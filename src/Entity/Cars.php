<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CarsRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarsRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields:['model'], message: "Another car already has this model, please modify it")]
class Cars
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, max: 255, minMessage: "Template must be longer than 1 character", maxMessage: "Template must not be longer than 255 characters")]    private ?string $model = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 3, max: 255, minMessage: "The mark must be longer than 3 characters", maxMessage: "The mark must not be longer than 255 characters")]    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(message: "A valid URL is required")]
    private ?string $coverImage = null;

    #[ORM\Column]
    private ?int $km = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $owners = null;

    #[ORM\Column]
    private ?float $cylinder = null;

    #[ORM\Column]
    private ?int $power = null;

    #[ORM\Column(length: 255)]
    private ?string $fuel = null;

    #[ORM\Column]
    #[Assert\Range(min: 2000, max: 2023, notInRangeMessage: "The original year of your car must be between 2000 and 2023")]    private ?int $year = null;

    #[ORM\Column(length: 255)]
    private ?string $transmission = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $options = null;

    #[ORM\OneToMany(mappedBy: 'car', targetEntity: Image::class, orphanRemoval: true)]
    #[Assert\Valid()]
    private Collection $images;

    #[ORM\Column(length: 255)]
    private ?string $slugBrand = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\OneToMany(mappedBy: 'car', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * Permet d'intialiser le slug automatiquement si on ne le donne pas
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if(empty($this->slug))
        {
            $slugify = new Slugify();
            //slugifier la marque et le modèle pour créer un slug "combiné"
            $brandSlug = $slugify->slugify($this->brand);
            $modelSlug = $slugify->slugify($this->model);

            $this->slug = $brandSlug . '-' . $modelSlug;
          
        }
        if(empty($this->slugBrand))
        {
            $slugify = new Slugify();
            //slugifier la marque et le modèle pour créer un slug "combiné"
            $slugBrand = $slugify->slugify($this->brand);
   

            $this->slugBrand = $slugBrand;
          
        }
    }
        /**
     * Permet d'obtenir le nom complet de l'utilisteur
     *
     * @return string
     */
    public function getFullCar(): string
    {
        return $this->brand." ".$this->model;
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getKm(): ?int
    {
        return $this->km;
    }

    public function setKm(int $km): static
    {
        $this->km = $km;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getOwners(): ?int
    {
        return $this->owners;
    }

    public function setOwners(int $owners): static
    {
        $this->owners = $owners;

        return $this;
    }

    public function getCylinder(): ?float
    {
        return $this->cylinder;
    }

    public function setCylinder(float $cylinder): static
    {
        $this->cylinder = $cylinder;

        return $this;
    }

    public function getPower(): ?int
    {
        return $this->power;
    }

    public function setPower(int $power): static
    {
        $this->power = $power;

        return $this;
    }

    public function getFuel(): ?string
    {
        return $this->fuel;
    }

    public function setFuel(string $fuel): static
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function setTransmission(string $transmission): static
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(string $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setCar($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getCar() === $this) {
                $image->setCar(null);
            }
        }

        return $this;
    }

    public function getSlugBrand(): ?string
    {
        return $this->slugBrand;
    }

    public function setSlugBrand(string $slugBrand): static
    {
        $this->slugBrand = $slugBrand;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }
     /**
     * Permet de récup la note d'une annonce
     *
     * @return integer
     */
    public function getAvgRatings(): int 
    {
        // calculer la sommes des notations
        // fonction array_reduce - permet réduire le tableau à une saule valeur (attention il faut un tableau pas une array Collection, pour la transformation on va utiliser toArray() - 2ème paramètre c'est la foonction pour chaque valeur, le 3ème valeur par défaut)
        $sum = array_reduce($this->comments->toArray(), function($total, $comment){
            return $total + $comment->getRating();
        },0);

        // faire la division pour avoir la moyenne (ternaire)
        if(count($this->comments) > 0) return $moyennne = round($sum / count($this->comments));
        return 0;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setCar($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCar() === $this) {
                $comment->setCar(null);
            }
        }

        return $this;
    }
}
