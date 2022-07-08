<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class OrderProduct
 * @package App\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="orders_menus")
 */
class OrderProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Menu", inversedBy="orderProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $menu;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return OrderProduct
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     * @return OrderProduct
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMenu(): Menu
    {
        return $this->menu;
    }

    /**
     * @param mixed $menu
     * @return OrderProduct
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    : int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return OrderProduct
     */
    public function setQuantity(int $quantity)
    : OrderProduct
    {
        $this->quantity = $quantity;
        return $this;
    }
}
