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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function profile(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $orders = $entityManager->getRepository(Order::class)->findBy([
            'user' => $this->getUser()
        ]);

        foreach ($orders as $order) {
            $ordersArray[$order->getId()] = [
                'id' => $order->getId(),
                'date' => $order->getOrderDate()->format('Y-m-d H:i:s'),
                'address' => $order->getAddress(),
                'amount' => $order->getAmount(),
                'status' => $order->getStatus(),
                'deliveryUser' => $order->getDeliveryUser() ? $order->getDeliveryUser()->getUserIdentifier() : ''
            ];
        }

        $restaurants = $entityManager->getRepository(Restaurant::class)->createQueryBuilder('r')
            ->select('r.id, r.name')
            ->join(Menu::class, 'm', 'WITH', 'm.restaurant = r')
            ->join(OrderProduct::class, 'p', 'WITH', 'p.menu = m')
            ->join(Order::class, 'o', 'WITH', 'p.order = o')
            ->andWhere('o.user = :user')->setParameter('user', $this->getUser())
            ->groupBy('r.id')
            ->getQuery()->getResult();

        return $this->render('home/profile.html.twig',[
            "user" => $this->getUser(),
            "orders" => $ordersArray ?? [],
            "restaurants" => $restaurants ?? []
        ]);
    }

    /**
     * @Route("/orders/{id}/products", name="get-order-products")
     */
    public function getOrderProducts(int $id, EntityManagerInterface $entityManager)
    {
        $orderProducts = $entityManager->getRepository(OrderProduct::class)->findBy([
            'order' => $id
        ]);

        foreach ($orderProducts as $orderProduct) {
            $products[] = [
                'name' => $orderProduct->getMenu()->getName(),
                'price' => $orderProduct->getMenu()->getPrice(),
                'quantity' => $orderProduct->getQuantity(),
                'img' => $orderProduct->getMenu()->getItemImage(),
                'restaurant' => $orderProduct->getMenu()->getRestaurant()->getName()
            ];
        }

        return $this->render('order-products-modal.html.twig', [
            'products' => $products ?? []
        ]);
    }


    /**
     * @Route("/edit-profile", name="edit-profile")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function editProfile(UserRepository $userRepository)
    {
        return $this->render('home/edit-profile.html.twig',[
            "user" => $this->getUser()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/upload-img", name="upload_img")
     */
    public function uploadImg(Request $request, UserRepository $userRepository, EntityManagerInterface  $entityManager)
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');
        $user = $this->getUser();

        $fileName = $user->getUsername() . '-' . $user->getId() . '.jpg';
        $uploadedFile->move(__DIR__ . '/../../public/images/users', $fileName);

        $user->setProfileImage($fileName);

        $entityManager->flush();

        return new JsonResponse([]);
    }

    /**
     * @param Request $request
     * @return void
     * @Route("/update-user", name="update_user")
     */
    public function updateUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $firstName = $data['firstName'];
        $user->setFirstName($firstName);
        $lastName = $data['lastName'];
        $user->setLastName($lastName);
        $username = $data['username'];
        $user->setUsername($username);
        $phone = $data['phone'];
        $user->setPhoneNumber($phone);
        $email = $data['email'];
        $user->setEmail($email);
//        $address = $data['address'];
//        $user->setAddress($address);
//        dd($user);
        $entityManager->flush();
        return new JsonResponse([]);


    }
}
