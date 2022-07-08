<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RestaurantRepository::class)
 */
class Restaurant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $restaurantLogoImage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $restaurantImage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRestaurantLogoImage(): ?string
    {
        return $this->restaurantLogoImage;
    }

    public function setRestaurantLogoImage(string $restaurantLogoImage): self
    {
        $this->restaurantLogoImage = $restaurantLogoImage;

        return $this;
    }

    public function getRestaurantImage(): ?string
    {
        return $this->restaurantImage;
    }

    public function setRestaurantImage(string $restaurantImage): self
    {
        $this->restaurantImage = $restaurantImage;

        return $this;
    }
    public function getRestaurantLogoPath()
    {
        return 'images/restaurant/' . $this->getRestaurantLogoImage();
    }
    public function getRestaurantImagePath()
    {
        return 'images/restaurant/' . $this->getRestaurantImage();
    }
}
