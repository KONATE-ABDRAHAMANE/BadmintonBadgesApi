<?php

namespace App\Entity;

use App\Repository\MatchsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchsRepository::class)]
class Matchs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $coach_user_id = null;

    #[ORM\Column]
    private ?\DateTime $date_match = null;

    #[ORM\Column]
    private ?int $winning_point = null;

    #[ORM\Column(length: 255)]
    private ?string $qr_code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoachUserId(): ?int
    {
        return $this->coach_user_id;
    }

    public function setCoachUserId(int $coach_user_id): static
    {
        $this->coach_user_id = $coach_user_id;

        return $this;
    }

    public function getDateMatch(): ?\DateTime
    {
        return $this->date_match;
    }

    public function setDateMatch(\DateTime $date_match): static
    {
        $this->date_match = $date_match;

        return $this;
    }

    public function getWinningPoint(): ?int
    {
        return $this->winning_point;
    }

    public function setWinningPoint(int $winning_point): static
    {
        $this->winning_point = $winning_point;

        return $this;
    }

    public function getQrCode(): ?string
    {
        return $this->qr_code;
    }

    public function setQrCode(string $qr_code): static
    {
        $this->qr_code = $qr_code;

        return $this;
    }
}
