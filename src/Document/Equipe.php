<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Equipe
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\ReferenceOne(targetDocument: Utilisateur::class)]
    private Utilisateur $coach;

    #[MongoDB\Field(type: 'string')]
    private string $nomEquipe;


    #[MongoDB\Field(type: 'date', nullable: true)]
    private ?\DateTime $dateCreation = null;

    #[MongoDB\Field(type: 'date', nullable: true)]
    private ?\DateTime $dateModification = null;

    public function getId(): string { return $this->id; }
    public function getCoach(): Utilisateur { return $this->coach; }
    public function setCoach(Utilisateur $coach): void { $this->coach = $coach; }
    public function getNomEquipe(): string { return $this->nomEquipe; }
    public function setNomEquipe(string $nomEquipe): void { $this->nomEquipe = $nomEquipe; }
    public function getDateCreation(): ?\DateTime { return $this->dateCreation; }
    public function setDateCreation(?\DateTime $dateCreation): void { $this->dateCreation = $dateCreation; }
    public function getDateModification(): ?\DateTime { return $this->dateModification; }
    public function setDateModification(?\DateTime $dateModification): void { $this->dateModification = $dateModification; }
}

?>