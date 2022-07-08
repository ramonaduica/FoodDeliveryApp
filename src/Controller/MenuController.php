<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Menu;
use App\Entity\OrderProduct;
use App\Entity\Restaurant;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/admin/restaurants/{id}/add-menu", name="add_menu_form")
     * @param Request $request
     * @param int $id
     */
    public function addMenuForm(Request $request, EntityManagerInterface $entityManager, int $id)
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($id);
        $categories = $entityManager->getRepository(Category::class)->findAll();
        $categoriesArray = [];
        foreach ($categories as $category) {
            $categoriesArray[$category->getId()] = $category->getCategoryName();
         }

        return $this->render('admin/add_menu.html.twig', [
            'restaurant_id' => $restaurant->getId(),
            'categories' => $categoriesArray,
            'restaurant_name' => $restaurant->getName(),
            'logo' => $restaurant->getRestaurantLogoPath(),
            'cover' => $restaurant->getRestaurantImagePath()
        ]);
    }

    /**
     * @Route("/admin/restaurants/{id}/menus", name="add_menu")
     * @param Request $request
     * @param int $id
     */
    public function saveMenu(Request $request, EntityManagerInterface $em, int $id)
    {
        $restaurant = $em->getRepository(Restaurant::class)->find($id);
        $data = $request->request->all();
        $menu = new Menu();

        $itemImage = $request->files->get('item-image');
        $itemImageName = uniqid() . '-' . $itemImage->getClientOriginalName();
        $itemImage->move(__DIR__ . '/../../public/images/food', $itemImageName);

        $menu->setCategory($em->getRepository(Category::class)->find($data['category']))
            ->setRestaurant($restaurant)
            ->setName($data['name'])
            ->setPrice($data['price'])
            ->setItemImage($itemImageName);

        $em->persist($menu);
        $em->flush();

        return $this->redirectToRoute('show_menu', ['id' => $id]);
    }

    /**
     * @Route("/admin/restaurants/{id}/menu", name="show_menu")
     * @param MenuRepository $menuRepository
     * @return Response
     */
    public function showMenu(MenuRepository $menuRepository, int $id, EntityManagerInterface $em)
    {
        $restaurant = $em->getRepository(Restaurant::class)->find($id);
        $restaurantId = $restaurant->getId();
        $menus = $menuRepository->findBy([
            'restaurant' => $restaurantId
        ]);

        $menusWithCategories = [];
        foreach ($menus as $menu) {
            $menusWithCategories[$menu->getCategory()->getCategoryName()][] = $menu;
        }

        return $this->render('admin/show_menu.html.twig',[
            "menus" => $menusWithCategories,
            "restaurant" => $restaurant
        ]);
    }


    /**
     * @Route("/search-menu/{name}", name="search_menu")
     */
    public function searchMenu(EntityManagerInterface $em, string $name)
    {
        $results = $em->getRepository(Menu::class)->createQueryBuilder('m')
            ->select('m.name', 'm.id as menu_id', 'm.price', 'm.itemImage', 'r.id as restaurant_id', 'r.name as restaurant_name')
            ->join(Restaurant::class, 'r', 'WITH', 'r.id = m.restaurant')
            ->where('m.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery()->getResult();

        foreach ($results as $result) {
            $search[$result['restaurant_id']]['restaurant_name'] = $result['restaurant_name'];
            $search[$result['restaurant_id']]['menus'][] = $result;

        }

        return $this->render('home/search-menu.html.twig', [
            'products' => $search ?? []
        ]);
    }




}
