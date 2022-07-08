<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * Require ROLE_ADMIN for all the actions of this controller
     *
     */
    public function adminDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // or add an optional message - seen by developers
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     *
     * @Route("/admin/orders", name="show_admin_orders")
     */
    public function showAdminOrders(EntityManagerInterface $entityManager)
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
                'deliveryUser' => $order->getDeliveryUser() ? $order->getDeliveryUser()->getEmail() : ''
            ];
        }

        return $this->render('admin/orders.html.twig',[
            "user" => $this->getUser(),
            "orders" => $ordersArray ?? [],
        ]);
    }


    /**
     * @Route("/admin/add-restaurant", name="add-restaurant")
     * @param RestaurantRepository $restaurantRepository
     * @return Response
     */
    public function addRestaurant(RestaurantRepository $restaurantRepository)
    {
        $restaurants = $restaurantRepository->findAll();
        return $this->render('admin/add_restaurant.html.twig', [
            "restaurants" => $restaurants
        ]);
    }


    /**
     * @param Request $request
     * @Route("/added-restaurant", name="added_restaurant")
     */
    public function addRestaurantDetails(Request $request, EntityManagerInterface $em)
    {
        $logo = $request->files->get('restaurant_image');
        $logoFileName = uniqid() . '-' . $logo->getClientOriginalName();
        $logo->move(__DIR__ . '/../../public/images/restaurant', $logoFileName);

        $cover = $request->files->get('restaurant_img');
        $fileName = uniqid() . '-' . $cover->getClientOriginalName();
        $cover->move(__DIR__ . '/../../public/images/restaurant', $fileName);

        $restaurant = new Restaurant();
        $data = $request->request->all();


        $name = $data['name'];
        $restaurant->setName($name);
        $shortDescription = $data['shortDescription'];
        $restaurant->setShortDescription($shortDescription);
//        $description = $data['description'];
//        $restaurant->setDescription($description);
        $restaurant->setRestaurantImage($fileName);
        $restaurant->setRestaurantLogoImage($logoFileName);

        $em->persist($restaurant);
        $em->flush();

        return $this->json([]);
    }

    /**
     * @param RestaurantRepository $restaurantRepository
     * @Route ("/admin/restaurants", name="restaurants")
     * @return Response
     */
    public function showRestaurants(RestaurantRepository $restaurantRepository)
    {
        $restaurants = $restaurantRepository->findAll();
        return $this->render('admin/restaurants.html.twig', [
            "restaurants" => $restaurants
        ]);
    }

    /**
     * @param Request $request
     * @param RestaurantRepository $restaurantRepository
     * @Route ("/admin/edit-restaurant/{id}", name="edit-restaurant")
     * @return Response
     */
    public function editRestaurant(Request $request, int $id, EntityManagerInterface $em, RestaurantRepository $restaurantRepository)
    {
        $restaurant = $restaurantRepository->find($id);
        $data = $request->request->all();
        $name = $data['name'];
        $restaurant->setName($name);
        $shortDescription = $data['shortDescription'];
        $restaurant->setShortDescription($shortDescription);

        $logo = $request->files->get('restaurant_img', false);
        if ($logo) {
            $logoFileName = uniqid() . '-' . $logo->getClientOriginalName();
            $logo->move(__DIR__ . '/../../public/images/restaurant', $logoFileName);
            $restaurant->setRestaurantLogoImage($logoFileName);
        }

        $cover = $request->files->get('restaurant_image', false);
        if ($cover) {
            $fileName = uniqid() . '-' . $cover->getClientOriginalName();
            $cover->move(__DIR__ . '/../../public/images/restaurant', $fileName);
            $restaurant->setRestaurantImage($fileName);
        }

        $em->flush();

        return new JsonResponse([]);
    }

    /**
     * @Route("/admin/delete/{id}", name="delete-restaurant")
     */
    public function deleteRestaurant(EntityManagerInterface $em, int $id, RestaurantRepository $restaurantRepository)
    {
        $restaurant = $restaurantRepository->find($id);
        $em->remove($restaurant);
        $em->flush();

        $this->addFlash('success', 'Restaurant was removed!');
        return $this->redirectToRoute('restaurants');
    }

}
