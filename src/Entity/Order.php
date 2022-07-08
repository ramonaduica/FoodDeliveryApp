<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table (name="`order`")
 */
class Order
{
    public const TRANSPORT_PRICE = 14.99;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private $user;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="delivery_user_id", referencedColumnName="id")
     *
     */
    private $deliveryUser;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column (type="datetime")
     */
    private $oderDate;

    /**
     * @var Menu[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderProduct", mappedBy="order", fetch="EXTRA_LAZY")
     */
    private $orderProducts;

    public function __construct() {
        $this->oderDate = new \DateTime();
        $this->orderProducts = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Order
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    : User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Order
     */
    public function setUser(User $user)
    : Order
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    : string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return Order
     */
    public function setAddress(string $address)
    : Order
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    : string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return Order
     */
    public function setPhoneNumber(string $phoneNumber)
    : Order
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Order
     */
    public function setStatus(string $status)
    : Order
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    : float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return Order
     */
    public function setAmount(float $amount)
    : Order
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return Menu[]
     */
    public function getProducts(): array
    {
        return $this->orderProducts;
    }

    /**
     * @param Menu[] $products
     * @return Order
     */
    public function setProducts(array $products)
    : Order
    {
        $this->orderProducts = $products;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOrderDate()
    : \DateTime
    {
        return $this->oderDate;
    }

    /**
     * @param \DateTime $oderDate
     * @return Order
     */
    public function setOrderDate(\DateTime $oderDate)
    : Order
    {
        $this->oderDate = $oderDate;
        return $this;
    }

    /**
     * @return User
     */
    public function getDeliveryUser(): ?User
    {
        return $this->deliveryUser;
    }

    /**
     * @param User $deliveryUser
     * @return Order
     */
    public function setDeliveryUser(User $deliveryUser): Order
    {
        $this->deliveryUser = $deliveryUser;
        return $this;
    }


}
