<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="order_chat_messages")
 */
class Chat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id")
     *
     */
    private $fromUser;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
     *
     */
    private $toUser;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Order")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var \DateTime
     *
     * @ORM\Column (type="datetime")
     */
    private $sendAt;

    public function __construct()
    {
        $this->sendAt = new \DateTime();
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
     * @return Chat
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return Chat
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return User
     */
    public function getFromUser(): User
    {
        return $this->fromUser;
    }

    /**
     * @param User $fromUser
     * @return Chat
     */
    public function setFromUser(User $fromUser): Chat
    {
        $this->fromUser = $fromUser;
        return $this;
    }

    /**
     * @return User
     */
    public function getToUser(): User
    {
        return $this->toUser;
    }

    /**
     * @param User $toUser
     * @return Chat
     */
    public function setToUser(User $toUser): Chat
    {
        $this->toUser = $toUser;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    : Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return Chat
     */
    public function setOrder(Order $order)
    : Chat
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSendAt()
    : \DateTime
    {
        return $this->sendAt;
    }

    /**
     * @param \DateTime $sendAt
     * @return Chat
     */
    public function setSendAt(\DateTime $sendAt)
    : Chat
    {
        $this->sendAt = $sendAt;
        return $this;
    }
}
