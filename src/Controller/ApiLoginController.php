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
use Symfony\Component\Security\Core\User\UserInterface;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        EncryptionService $encryptionService,
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {

        $data =  json_decode($request->getContent());
        $key = base64_decode($data->key);

        $email = $encryptionService->decrypt($data->encryptedEmail, $data->nonceEmail, $key);
        $password = $encryptionService->decrypt($data->encryptedPassword, $data->noncePassword, $key);

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);

        $isValidUser = $passwordHasher->isPasswordValid($user, $password);

        if (!$user instanceof UserInterface || !$isValidUser) {
            return $this->json(['message' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $jwtManager->create($user);

        return $this->json([
            'token' => $token,
        ]);
    }
}
