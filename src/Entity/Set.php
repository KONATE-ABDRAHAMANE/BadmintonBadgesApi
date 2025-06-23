<?php

namespace App\Entity;

use App\Repository\SetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SetRepository::class)]
class Set
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $match_id = null;

    #[ORM\Column]
    private ?int $set_number = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatchId(): ?int
    {
        return $this->match_id;
    }

    public function setMatchId(int $match_id): static
    {
        $this->match_id = $match_id;

        return $this;
    }

    public function getSetNumber(): ?int
    {
        return $this->set_number;
    }

    public function setSetNumber(int $set_number): static
    {
        $this->set_number = $set_number;

        return $this;
    }
}
