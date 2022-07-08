<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeliveryController extends AbstractController
{
    /**
     * Require ROLE_COURIER for all the actions of this controller
     *
     */
    public function courierDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_COURIER');

        // or add an optional message - seen by developers
        $this->denyAccessUnlessGranted('ROLE_COURIER', null, 'User tried to access a page without having ROLE_COURIER');
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     *
     * @Route("/courier/orders", name="show_courier_orders")
     */
    public function showCourierOrders(EntityManagerInterface $entityManager)
    {
        $orders = $entityManager->getRepository(Order::class)->findAll();
        foreach ($orders as $order) {
            $ordersArray[$order->getId()] = [
                'id' => $order->getId(),
                'date' => $order->getOrderDate()->format('Y-m-d H:i:s'),
                'address' => $order->getAddress(),
                'amount' => $order->getAmount(),
                'user' => $order->getUser()->getEmail(),
                'status' => $order->getStatus(),
                'deliveryUser' => $order->getDeliveryUser()
            ];
        }

        return $this->render('courier/orders.html.twig',[
            "user" => $this->getUser(),
            "orders" => $ordersArray ?? [],
        ]);
    }

    /**
     * @Route("/courier/take-order/{id}", name="take_order_for_delivery")
     */
    public function takeOrderForDelivery(Request $request, EntityManagerInterface $entityManager, int $id)
    {
        $order = $entityManager->getRepository(Order::class)->find($id);

        $status = "Comanda preluata de curier";
        $order->setStatus($status)
            ->setDeliveryUser($this->getUser());

        $entityManager->flush();

        return $this->json([]);

    }

    /**
     * @Route("/courier/deliver-order/{id}", name="deliver_order")
     */
    public function deliverOrder(EntityManagerInterface $entityManager, int $id)
    {
        $order = $entityManager->getRepository(Order::class)->find($id);
        $status = "Comanda Livrata";
        if ($this->getUser() != $order->getDeliveryUser()) {
            return;
        }
        $order->setStatus($status);
        $entityManager->flush();

        return $this->json([]);
    }

}