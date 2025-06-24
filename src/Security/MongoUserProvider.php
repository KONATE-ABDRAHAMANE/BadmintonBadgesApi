<?php

namespace App\Security;

use App\Document\Utilisateur;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class MongoUserProvider implements UserProviderInterface
{
    public function __construct(private DocumentManager $dm) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->dm->getRepository(Utilisateur::class)->findOneBy(['email' => $identifier]);

        if (!$user) {
            throw new UserNotFoundException(sprintf('Utilisateur "%s" introuvable.', $identifier));
        }

        return $user;
    }

    // Gardé pour compatibilité Symfony < 5.3
    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances de "%s" ne sont pas supportées.', get_class($user)));
        }

        $refreshedUser = $this->dm->getRepository(Utilisateur::class)->find($user->getId());

        if (!$refreshedUser) {
            throw new UserNotFoundException(sprintf('Utilisateur avec id "%s" introuvable.', $user->getId()));
        }

        return $refreshedUser;
    }

    public function supportsClass(string $class): bool
    {
        return $class === Utilisateur::class || is_subclass_of($class, Utilisateur::class);
    }
}
