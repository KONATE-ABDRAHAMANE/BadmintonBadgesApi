<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class JoueurEquipe
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\ReferenceOne(targetDocument: Equipe::class)]
    private Equipe $equipe;

    #[MongoDB\ReferenceOne(targetDocument: Utilisateur::class)]
    private Utilisateur $utilisateur;

    public function getId(): string { return $this->id; }
    public function getEquipe(): Equipe { return $this->equipe; }
    public function setEquipe(Equipe $equipe): void { $this->equipe = $equipe; }
    public function getUtilisateur(): Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(Utilisateur $utilisateur): void { $this->utilisateur = $utilisateur; }
}

?>