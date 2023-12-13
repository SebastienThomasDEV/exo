<?php

namespace App\Controller;

use App\Entity\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, Security $security): Response
    {
        if ($request->getMethod() === "POST" && $this->getUser() !== null) {
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('auth/login.html.twig', [
            'message' => '',
        ]);
    }
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $message = '';
        if ($request->getMethod() === "POST") {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            if ($username === "" || $password === "") {
                $message = "les champs ne doivent pas être vide";
            } else {
                $user = new User();
                $user->setUsername($username);
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
                $userRepository->save($user);
                $message = "l'utilisateur à été crée";
            }
        }
        return $this->render('auth/register.html.twig', [
            'message' => $message
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security) {
        $security->logout(false);
        return $this->redirectToRoute('app_home');
    }

}
