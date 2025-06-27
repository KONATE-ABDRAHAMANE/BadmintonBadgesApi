<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/joueurs-equipes')]
class JoueurEquipeController extends AbstractController
{
    #[Route('/joueur/equipe', name: 'app_joueur_equipe')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/JoueurEquipeController.php',
        ]);
    }
}
