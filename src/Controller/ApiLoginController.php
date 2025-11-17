<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EncryptionService;
use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserProviderInterface $userProvider,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {

        $email = $request->request->get('email'); // voir decrypt dans RequestSubscriber
        $password = $request->request->get('password'); // voir decrypt dans RequestSubscriber

        $user = $userProvider->loadUserByIdentifier($email);
        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        $token = $jwtManager->create($user);

        return $this->json([
            'token' => $token,
        ]);
    }
}
