<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    /**
     * @Route("/orders/{id}/chat/add_message", name="chat_message")
     */
    public function addChatMessage(EntityManagerInterface $entityManager, Request $request, int $id)
    {
        $order = $entityManager->getRepository(Order::class)->find($id);

        $fromUser = $this->getUser();
        if (in_array('ROLE_COURIER', $fromUser->getRoles())) {
            $toUser = $order->getUser();
        } else {
            $toUser = $order->getDeliveryUser();
        }

        $chat = new Chat();
        $chat->setMessage($request->request->get('message'))
            ->setOrder($order)
            ->setFromUser($fromUser)
            ->setToUser($toUser);

        $entityManager->persist($chat);
        $entityManager->flush();


        return $this->json([]);
    }

    /**
     * @Route("/orders/{id}/chat/messages", name="get_messages")
     */
    public function getMessages(EntityManagerInterface $entityManager, Request $request, int $id)
    {
        $messages = $entityManager->getRepository(Chat::class)->findBy([
            'order' => $id
        ]);

        foreach ($messages as $message) {
            $msgs[] = [
                'message' => $message->getMessage(),
                'id' => $message->getId(),
                'is_current_user' => $message->getFromUser() == $this->getUser()
            ];
        }

        return $this->json($msgs ?? []);
    }

    /**
     * @Route("/orders/{id}/chat/new-messages", name="get_new_messages")
     */
    public function getNewMessages(EntityManagerInterface $entityManager, Request $request, int $id)
    {
        $order = $entityManager->getRepository(Order::class)->find($id);

        $messages = $entityManager->getRepository(Chat::class)->createQueryBuilder('c')
            ->select('c.message, c.id')
            ->join('c.toUser', 'u')
            ->where('u.email = :toUser')->setParameter('toUser', $this->getUser()->getUserIdentifier())
            ->andWhere('c.id > :lastId')->setParameter('lastId', $request->query->get('lastId'))
            ->andWhere('c.order = :order')->setParameter('order', $order)
            ->getQuery()->getResult();

        return $this->json($messages);
    }
}
