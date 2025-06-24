<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // Cette méthode ne sera jamais appelée car LexikJWTAuthenticationBundle intercepte /api/login
        // Si jamais appelée, renvoyer un message clair.
        return new JsonResponse(['message' => 'Authentification via JWT'], 401);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // Avec JWT, logout se fait côté client (effacer le token)
        // Pas de session à détruire côté serveur
        return new JsonResponse(['message' => 'Déconnecté avec succès']);
    }
}
