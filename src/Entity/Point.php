<?php

namespace App\Entity;

use App\Repository\PointRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointRepository::class)]
class Point
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $set_id = null;

    #[ORM\Column]
    private ?int $marker_player_id = null;

    #[ORM\Column(length: 255)]
    private ?string $point_type = null;

    #[ORM\Column(length: 255)]
    private ?string $colour_type = null;

    #[ORM\Column]
    private ?int $x_coordinate = null;

    #[ORM\Column]
    private ?int $y_coordinate = null;

    #[ORM\Column(length: 255)]
    private ?string $area_terrain = null;

    #[ORM\Column(length: 255)]
    private ?string $hit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSetId(): ?int
    {
        return $this->set_id;
    }

    public function setSetId(int $set_id): static
    {
        $this->set_id = $set_id;

        return $this;
    }

    public function getMarkerPlayerId(): ?int
    {
        return $this->marker_player_id;
    }

    public function setMarkerPlayerId(int $marker_player_id): static
    {
        $this->marker_player_id = $marker_player_id;

        return $this;
    }

    public function getPointType(): ?string
    {
        return $this->point_type;
    }

    public function setPointType(string $point_type): static
    {
        $this->point_type = $point_type;

        return $this;
    }

    public function getColourType(): ?string
    {
        return $this->colour_type;
    }

    public function setColourType(string $colour_type): static
    {
        $this->colour_type = $colour_type;

        return $this;
    }

    public function getXCoordinate(): ?int
    {
        return $this->x_coordinate;
    }

    public function setXCoordinate(int $x_coordinate): static
    {
        $this->x_coordinate = $x_coordinate;

        return $this;
    }

    public function getYCoordinate(): ?int
    {
        return $this->y_coordinate;
    }

    public function setYCoordinate(int $y_coordinate): static
    {
        $this->y_coordinate = $y_coordinate;

        return $this;
    }

    public function getAreaTerrain(): ?string
    {
        return $this->area_terrain;
    }

    public function setAreaTerrain(string $area_terrain): static
    {
        $this->area_terrain = $area_terrain;

        return $this;
    }

    public function getHit(): ?string
    {
        return $this->hit;
    }

    public function setHit(string $hit): static
    {
        $this->hit = $hit;

        return $this;
    }
}
