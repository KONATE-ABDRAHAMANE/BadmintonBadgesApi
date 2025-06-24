<?php

namespace App\Controller;

use App\Document\Utilisateur;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class UtilisateurController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    // ============================
    // Routes Admin (Admins + Coachs)
    // ============================
    #[Route('/admin', name: 'api_admin_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function listAdmins(DocumentManager $dm): JsonResponse
    {
        // Admin peut voir les Admins et Coachs
        $users = $dm->getRepository(Utilisateur::class)
            ->createQueryBuilder()
            ->field('roles')->in(['ROLE_ADMIN', 'ROLE_COACH'])
            ->getQuery()
            ->execute();

        return $this->json($this->serializeUtilisateurs($users));
    }

    #[Route('/admin', name: 'api_admin_create', methods: ['POST'])]
    //#[IsGranted('ROLE_ADMIN')]
    public function createAdmin(
        Request $request,
        DocumentManager $dm,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // On autorise uniquement les rôles admin et coach ici
        $allowedRoles = ['ROLE_ADMIN', 'ROLE_COACH'];
        $roles = $data['roles'] ?? ['ROLE_COACH'];

        if (array_diff($roles, $allowedRoles)) {
            return $this->json(['error' => 'Rôles non autorisés pour cette route'], Response::HTTP_FORBIDDEN);
        }

        $user = new Utilisateur();
        $user->setNom($data['nom'] ?? '');
        $user->setEmail($data['email']);
        $user->setRoles($roles);
        $user->setPlainPassword($data['password'] ?? '');

        $errors = $this->validator->validate($user, null, ['registration']);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();

        $dm->persist($user);
        $dm->flush();

        return $this->json($this->serializeUtilisateur($user), Response::HTTP_CREATED);
    }

    #[Route('/admins/{id}', name: 'api_admin_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAdmin(Utilisateur $user): JsonResponse
    {
        if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_COACH', $user->getRoles())) {
            return $this->json(['error' => 'Utilisateur non admin/coach'], Response::HTTP_FORBIDDEN);
        }

        return $this->json($this->serializeUtilisateur($user));
    }

    #[Route('/admins/{id}', name: 'api_admin_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateAdmin(
        Request $request,
        Utilisateur $user,
        DocumentManager $dm,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_COACH', $user->getRoles())) {
            return $this->json(['error' => 'Utilisateur non admin/coach'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) $user->setNom($data['nom']);
        if (isset($data['email'])) $user->setEmail($data['email']);

        if (isset($data['roles'])) {
            // Autoriser que les roles coach ou admin ici
            if (!array_diff($data['roles'], ['ROLE_ADMIN', 'ROLE_COACH'])) {
                $user->setRoles($data['roles']);
            }
        }

        if (isset($data['password'])) {
            $user->setPlainPassword($data['password']);
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
        }

        $dm->flush();

        return $this->json($this->serializeUtilisateur($user));
    }

    #[Route('/admins/{id}', name: 'api_admin_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAdmin(Utilisateur $user, DocumentManager $dm): JsonResponse
    {
        // Sécurité : admin ne peut pas supprimer un autre admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Suppression de compte admin interdite'], Response::HTTP_FORBIDDEN);
        }

        $dm->remove($user);
        $dm->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    // ============================
    // Routes Coach (Joueurs)
    // ============================
    #[Route('/coachs', name: 'api_coach_index', methods: ['GET'])]
    #[IsGranted('ROLE_COACH')]
    public function listJoueurs(DocumentManager $dm): JsonResponse
    {
        $joueurs = $dm->getRepository(Utilisateur::class)
            ->createQueryBuilder()
            ->field('roles')->equals('ROLE_JOUEUR')
            ->getQuery()
            ->execute();

        return $this->json($this->serializeUtilisateurs($joueurs));
    }

    #[Route('/coachs', name: 'api_coach_create', methods: ['POST'])]
    #[IsGranted('ROLE_COACH')]
    public function createJoueur(
        Request $request,
        DocumentManager $dm,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $user = new Utilisateur();
        $user->setNom($data['nom'] ?? '');
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_JOUEUR']);
        $user->setPlainPassword($data['password'] ?? '');

        $errors = $this->validator->validate($user, null, ['registration']);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();

        $dm->persist($user);
        $dm->flush();

        return $this->json($this->serializeUtilisateur($user), Response::HTTP_CREATED);
    }

    #[Route('/coachs/{id}', name: 'api_coach_show', methods: ['GET'])]
    #[IsGranted('ROLE_COACH')]
    public function showJoueur(Utilisateur $user): JsonResponse
    {
        if (!in_array('ROLE_JOUEUR', $user->getRoles())) {
            return $this->json(['error' => 'Utilisateur non joueur'], Response::HTTP_FORBIDDEN);
        }

        return $this->json($this->serializeUtilisateur($user));
    }

    #[Route('/coachs/{id}', name: 'api_coach_update', methods: ['PUT'])]
    #[IsGranted('ROLE_COACH')]
    public function updateJoueur(
        Request $request,
        Utilisateur $user,
        DocumentManager $dm,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        if (!in_array('ROLE_JOUEUR', $user->getRoles())) {
            return $this->json(['error' => 'Utilisateur non joueur'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) $user->setNom($data['nom']);
        if (isset($data['email'])) $user->setEmail($data['email']);
        // Un coach ne peut pas changer les roles ici

        if (isset($data['password'])) {
            $user->setPlainPassword($data['password']);
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
        }

        $dm->flush();

        return $this->json($this->serializeUtilisateur($user));
    }

    #[Route('/coachs/{id}', name: 'api_coach_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_COACH')]
    public function deleteJoueur(Utilisateur $user, DocumentManager $dm): JsonResponse
    {
        if (!in_array('ROLE_JOUEUR', $user->getRoles())) {
            return $this->json(['error' => 'Suppression non autorisée'], Response::HTTP_FORBIDDEN);
        }

        $dm->remove($user);
        $dm->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    // ============================
    // Routes Joueur (auto-read seul)
    // ============================
    #[Route('/joueurs/{id}', name: 'api_joueur_show', methods: ['GET'])]
    #[IsGranted('ROLE_JOUEUR')]
    public function showJoueurSelf(Utilisateur $user): JsonResponse
    {
        $current = $this->getUser();
        if ($current->getId() !== $user->getId() || !in_array('ROLE_JOUEUR', $user->getRoles())) {
            return $this->json(['error' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        }

        return $this->json($this->serializeUtilisateur($user));
    }

    // ============================
    // Utilitaires
    // ============================
    private function serializeUtilisateur(Utilisateur $utilisateur): array
    {
        return [
            'id' => $utilisateur->getId(),
            'nom' => $utilisateur->getNom(),
            'email' => $utilisateur->getEmail(),
            'roles' => $utilisateur->getRoles(),
        ];
    }

    private function serializeUtilisateurs(iterable $utilisateurs): array
    {
        return array_map(
            fn($user) => $this->serializeUtilisateur($user),
            is_array($utilisateurs) ? $utilisateurs : iterator_to_array($utilisateurs)
        );
    }
}
