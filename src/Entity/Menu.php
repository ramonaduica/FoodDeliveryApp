<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
class Menu
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
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant", cascade={"remove"})
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $restaurant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $itemImage;

    /**
     * @var OrderProduct[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\OrderProduct", mappedBy="menu")
     */
    private $orderProducts;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private $category;

    public function __construct() {
        $this->orders = new ArrayCollection();
    }

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param mixed $restaurant
     * @return Menu
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;
        return $this;
    }

    public function getItemImage(): ?string
    {
        return $this->itemImage;
    }

    public function setItemImage(string $itemImage): self
    {
        $this->itemImage = $itemImage;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     * @return Menu
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }
}
