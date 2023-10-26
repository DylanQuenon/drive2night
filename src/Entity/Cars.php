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
#[UniqueEntity(fields:['model'], message:"Une autre voiture possède déjà ce model, merci de la modifier")]
class Cars
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, max: 255, minMessage:"Le modèle doit faire plus de 1 caractère", maxMessage: "Le modèle ne doit pas faire plus de 255 caractères")]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 3, max: 255, minMessage:"La marque doit faire plus de 3 caractères", maxMessage: "La marque ne doit pas faire plus de 255 caractères")]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(message: "Il faut une URL valide")]
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
    #[Assert\Range(min: 2000, max: 2023, notInRangeMessage: "L'année d'origine de votre voiture doit être comprise entre 2000 et 2023")]
    private ?int $year = null;

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

    public function __construct()
    {
        $this->images = new ArrayCollection();
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
}
