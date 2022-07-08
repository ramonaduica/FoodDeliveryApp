<?php
// src/Controller/RegistrationController.php
namespace App\Controller;

// ...
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface  $entityManager)
    {
        $user = new User();
        $user->setEmail($request->request->get('username'))
            ->setFirstName($request->request->get('firstName'))
            ->setLastName($request->request->get('lastName'));
        $plaintextPassword = $request->request->get('password');
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('login');
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/register-form", name="register_form")
     */
    public function registerForm()
    {
        return $this->render("home/register.html.twig");
    }
    public function delete(UserPasswordHasherInterface $passwordHasher, UserInterface $user, Request $request)
    {
        // ... e.g. get the password from a "confirm deletion" dialog
        $plaintextPassword = $request->getPassword();
            if (!$passwordHasher->isPasswordValid($user, $plaintextPassword)) {
                throw new AccessDeniedHttpException();
            }
        }
}

