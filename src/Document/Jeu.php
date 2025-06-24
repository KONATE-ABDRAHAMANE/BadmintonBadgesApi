<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Jeu
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\ReferenceOne(targetDocument: Rencontre::class)]
    private Rencontre $match;

    #[MongoDB\Field(type: 'int')]
    private int $numeroSet;

    public function getId(): string { return $this->id; }
    public function getMatch(): Rencontre { return $this->match; }
    public function setMatch(Rencontre $match): void { $this->match = $match; }
    public function getNumeroSet(): int { return $this->numeroSet; }
    public function setNumeroSet(int $numeroSet): void { $this->numeroSet = $numeroSet; }
}

?>