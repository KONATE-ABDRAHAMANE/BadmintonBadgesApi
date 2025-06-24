<?php

namespace App\Controller;

use App\Document\Equipe;
use App\Document\Utilisateur;
use App\Document\JoueurEquipe;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/equipes')]
class EquipeController extends AbstractController
{
    #[Route('', name: 'create_equipe', methods: ['POST'])]
    #[IsGranted('ROLE_COACH')]
    public function create(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $equipe = new Equipe();
        $equipe->setNomEquipe($data['nomEquipe'] ?? 'Sans nom');
        $equipe->setCoach($this->getUser());
        $equipe->setDateCreation(new \DateTime());

        $dm->persist($equipe);
        $dm->flush();

        return $this->json(['message' => 'Équipe créée avec succès', 'id' => $equipe->getId()]);
    }

    #[Route('', name: 'list_equipes', methods: ['GET'])]
    #[IsGranted('ROLE_COACH')]
    public function list(DocumentManager $dm): JsonResponse
    {
        $equipes = $dm->getRepository(Equipe::class)->findBy(['coach.id' => $this->getUser()->getId()]);
        $result = [];

        foreach ($equipes as $equipe) {
            $joueurs = $dm->getRepository(JoueurEquipe::class)->findBy(['equipe.id' => $equipe->getId()]);
            $joueursData = [];

            foreach ($joueurs as $je) {
                $utilisateur = $je->getUtilisateur();
                $joueursData[] = [
                    'id' => $utilisateur->getId(),
                    'email' => $utilisateur->getEmail(),
                    'nom' => $utilisateur->getNom(),
                    'prenom' => $utilisateur->getPrenom(),
                ];
            }

            $result[] = [
                'id' => $equipe->getId(),
                'nomEquipe' => $equipe->getNomEquipe(),
                'dateCreation' => $equipe->getDateCreation()?->format('Y-m-d H:i:s'),
                'joueurs' => $joueursData
            ];
        }

        return $this->json($result);
    }

    #[Route('/{id}', name: 'update_equipe', methods: ['PUT'])]
    #[IsGranted('ROLE_COACH')]
    public function update(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        $equipe = $dm->getRepository(Equipe::class)->find($id);

        if (!$equipe || $equipe->getCoach()->getId() !== $this->getUser()->getId()) {
            return $this->json(['message' => 'Équipe non trouvée ou non autorisé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $equipe->setNomEquipe($data['nomEquipe'] ?? $equipe->getNomEquipe());
        $equipe->setDateModification(new \DateTime());

        $dm->flush();

        return $this->json(['message' => 'Équipe mise à jour']);
    }

    #[Route('/{id}', name: 'delete_equipe', methods: ['DELETE'])]
    #[IsGranted('ROLE_COACH')]
    public function delete(string $id, DocumentManager $dm): JsonResponse
    {
        $equipe = $dm->getRepository(Equipe::class)->find($id);

        if (!$equipe || $equipe->getCoach()->getId() !== $this->getUser()->getId()) {
            return $this->json(['message' => 'Équipe non trouvée ou non autorisé'], 404);
        }

        $associations = $dm->getRepository(JoueurEquipe::class)->findBy(['equipe.id' => $id]);
        foreach ($associations as $assoc) {
            $dm->remove($assoc);
        }

        $dm->remove($equipe);
        $dm->flush();

        return $this->json(['message' => 'Équipe supprimée avec ses joueurs associés']);
    }

    #[Route('/{id}/ajouter-joueur', name: 'ajouter_joueur', methods: ['POST'])]
    #[IsGranted('ROLE_COACH')]
    public function ajouterJoueur(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        $equipe = $dm->getRepository(Equipe::class)->find($id);
        if (!$equipe || $equipe->getCoach()->getId() !== $this->getUser()->getId()) {
            return $this->json(['message' => 'Équipe non trouvée ou non autorisé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        if (!$email) {
            return $this->json(['message' => 'Email requis'], 400);
        }

        $joueur = $dm->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
        if (!$joueur || !in_array('ROLE_JOUEUR', $joueur->getRoles(), true)) {
            return $this->json(['message' => 'Joueur introuvable ou rôle invalide'], 404);
        }

        $existeDeja = $dm->getRepository(JoueurEquipe::class)->findOneBy([
            'equipe.id' => $id,
            'utilisateur.id' => $joueur->getId()
        ]);
        if ($existeDeja) {
            return $this->json(['message' => 'Ce joueur est déjà dans l\'équipe'], 400);
        }

        $je = new JoueurEquipe();
        $je->setEquipe($equipe);
        $je->setUtilisateur($joueur);
        $dm->persist($je);
        $dm->flush();

        return $this->json(['message' => 'Joueur ajouté à l\'équipe']);
    }

    #[Route('/{id}/retirer-joueur', name: 'retirer_joueur', methods: ['POST'])]
    #[IsGranted('ROLE_COACH')]
    public function retirerJoueur(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        $equipe = $dm->getRepository(Equipe::class)->find($id);
        if (!$equipe || $equipe->getCoach()->getId() !== $this->getUser()->getId()) {
            return $this->json(['message' => 'Équipe non trouvée ou non autorisé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        if (!$email) {
            return $this->json(['message' => 'Email requis'], 400);
        }

        $joueur = $dm->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
        if (!$joueur) {
            return $this->json(['message' => 'Joueur introuvable'], 404);
        }

        $association = $dm->getRepository(JoueurEquipe::class)->findOneBy([
            'equipe.id' => $id,
            'utilisateur.id' => $joueur->getId()
        ]);

        if (!$association) {
            return $this->json(['message' => 'Ce joueur n\'est pas associé à cette équipe'], 404);
        }

        $dm->remove($association);
        $dm->flush();

        return $this->json(['message' => 'Joueur retiré de l\'équipe']);
    }
}
