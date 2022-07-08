<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Restaurant;
use App\Entity\Review;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class OrderController extends AbstractController
{
    /**
     * @Route("/add-to-cart", name="add_to_cart")
     */
    public function addToCart(Request $request, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $data = json_decode($request->getContent(), true);

        $menu = $entityManager->getRepository(Menu::class)->find($data['menuId']);

        $cart = $session->get('cart', []);
        if ($cart[$menu->getRestaurant()->getId()]['menus'][$menu->getId()] ?? false) {
            $cart[$menu->getRestaurant()->getId()]['menus'][$menu->getId()]['quantity'] += $data['quantity'];
        } else {
            $cart[$menu->getRestaurant()->getId()]['restaurant_name'] = $menu->getRestaurant()->getName();
            $cart[$menu->getRestaurant()->getId()]['menus'][$menu->getId()] = [
                'menu' => $menu->getName(),
                'price' => $menu->getPrice(),
                'quantity' => $data['quantity'],
                'menuImage' => $menu->getItemImage()
            ];
        }

        $session->set('cart', $cart);

        return new JsonResponse([]);
    }

    /**
     * @Route("/get-cart", name="get_cart")
     */
    public function getCart(SessionInterface $session)
    {
        return new JsonResponse($session->get('cart', []));
    }

    /**
     * @Route("/show-cart", name="show_cart")
     */
    public function showOrder(SessionInterface $session)
    {
        $cart = $session->get('cart', []);

        $sum = 0;
        foreach ($cart as $restaurant) {
            foreach ($restaurant['menus'] as $menu) {
                $sum += (float)$menu['quantity'] * (float)$menu['price'];
            }
        }

        return $this->render('home/show-order.html.twig', [
            'restaurant_products' => $cart,
            'total_price' => $sum,
            'transport_price' => 14.99,
            'total_price_with_transport' => $sum + 14.99
        ]);
    }

    /**
     * @param SessionInterface $session
     * @Route("/get-order", name="get_order")
     */
    public function getOrder(SessionInterface $session)
    {
        return new JsonResponse($session->get('cart', []));
    }

    /**
     * @Route("/menus/{id}/delete-item", name="delete_menu_from_cart")
     */
    public function deleteFromCart(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, int $id)
    {
        $menu = $entityManager->getRepository(Menu::class)->find($id);
        $cart = $session->get('cart', []);
        $session->remove('cart');

        unset($cart[$menu->getRestaurant()->getId()]['menus'][$id]);
        if (empty($cart[$menu->getRestaurant()->getId()]['menus'])) {
            unset($cart[$menu->getRestaurant()->getId()]);
        }

        $session->set('cart', $cart);

        return new JsonResponse([]);
    }

    /**
     * @Route("/orders/save", name="save_order")
     */
    public function saveOrder(Request $request, SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $cart = $session->get('cart', []);
        $data = json_decode($request->getContent(), true);
        $order = new Order();
        $order->setUser($this->getUser())
            ->setAddress($data['address'])
            ->setPhoneNumber($data['phoneNumber']);
        $entityManager->persist($order);

        $sum = 0;
        foreach ($cart as $restaurant) {
            foreach ($restaurant['menus'] as $menuId => $menuArray) {
                $sum += (float)$menuArray['quantity'] * (float)$menuArray['price'];
                $menu = $entityManager->getRepository(Menu::class)->find($menuId);

                $orderProduct = new OrderProduct();
                $orderProduct->setMenu($menu)
                    ->setOrder($order)
                    ->setQuantity((float)$menuArray['quantity']);

                $entityManager->persist($orderProduct);

            }
        }
        $order->setAmount($sum + Order::TRANSPORT_PRICE);
        $entityManager->flush();
        $session->remove('cart');

        return new JsonResponse();
    }

    /**
     * @Route("/add-review", name="add_review")
     */
    public function addReview(Request $request, EntityManagerInterface $em)
    {
        $data = $request->request->all();

        $review = new Review();
        $rating = $data['rating'];
        $restaurant = $em->getRepository(Restaurant::class)->find($data['restaurant']);

        $review->setRating($rating)
            ->setRestaurant($restaurant);

        $em->persist($review);
        $em->flush();

        return $this->json([]);
    }
}
