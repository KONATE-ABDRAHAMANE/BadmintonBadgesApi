<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $coach_user_id = null;

    #[ORM\Column(length: 255)]
    private ?string $team_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTime $date_created = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date_modified = null;

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

    public function getTeamName(): ?string
    {
        return $this->team_name;
    }

    public function setTeamName(string $team_name): static
    {
        $this->team_name = $team_name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTime $date_created): static
    {
        $this->date_created = $date_created;

        return $this;
    }

    public function getDateModified(): ?\DateTime
    {
        return $this->date_modified;
    }

    public function setDateModified(?\DateTime $date_modified): static
    {
        $this->date_modified = $date_modified;

        return $this;
    }
}
