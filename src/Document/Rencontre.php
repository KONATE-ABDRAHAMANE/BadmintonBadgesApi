<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Rencontre
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\ReferenceOne(targetDocument: Utilisateur::class)]
    private Utilisateur $coach;

    #[MongoDB\Field(type: 'date')]
    private \DateTime $dateMatch;

    #[MongoDB\Field(type: 'int')]
    private int $pointGagnant;

    #[MongoDB\Field(type: 'string')]
    private string $qrCode;

    public function getId(): string { return $this->id; }
    public function getCoach(): Utilisateur { return $this->coach; }
    public function setCoach(Utilisateur $coach): void { $this->coach = $coach; }
    public function getDateMatch(): \DateTime { return $this->dateMatch; }
    public function setDateMatch(\DateTime $dateMatch): void { $this->dateMatch = $dateMatch; }
    public function getPointGagnant(): int { return $this->pointGagnant; }
    public function setPointGagnant(int $point): void { $this->pointGagnant = $point; }
    public function getQrCode(): string { return $this->qrCode; }
    public function setQrCode(string $qrCode): void { $this->qrCode = $qrCode; }
}
?>