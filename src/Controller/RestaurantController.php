<?php


namespace App\Controller;


use App\Entity\Menu;
use App\Entity\Restaurant;
use App\Entity\Review;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
    /**
     * @Route("/restaurants", name="show_restaurants")
     * @param Request $request
     * @return Response
     */
    public function showRestaurants(RestaurantRepository $restaurantRepository, EntityManagerInterface $em): Response
    {
        $restaurants = $restaurantRepository->findAll();

        $reviews = $em->getRepository(Review::class)->createQueryBuilder('rw')
            ->select('rw.id, r.name, r.id as restaurant_id, rw.rating, AVG(rw.rating) as average')
            ->join(Restaurant::class, 'r', 'WITH', 'r.id = rw.restaurant')
            ->groupBy('rw.restaurant')
            ->getQuery()->getResult();

        $reviews = array_column($reviews, null, 'restaurant_id');

        return $this->render('home/restaurants.html.twig', [
            "restaurants" => $restaurants,
            "reviews" => $reviews
        ]);
    }


    /**
     * @Route("/restaurants/{id}", name="restaurant_category")
     * @param Request $request
     * @return Response
     */
    public function restaurant(EntityManagerInterface $em, int $id)
    {
        $restaurant = $em->getRepository(Restaurant::class)->find($id);

        $menus = $em->getRepository(Menu::class)->findBy([
            'restaurant' => $restaurant
        ]);

        $menusWithCategories = [];
        foreach ($menus as $menu) {
            $menusWithCategories[$menu->getCategory()->getCategoryName()][] = $menu;
        }

        return $this->render('home/restaurant.html.twig',[
            "menus" => $menusWithCategories,
            "restaurant" => $restaurant
        ]);
    }



}
