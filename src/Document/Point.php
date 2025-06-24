<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Point
{
    #[MongoDB\Id(strategy: 'AUTO')]
    private ?string $id = null;

    #[MongoDB\ReferenceOne(targetDocument: Jeu::class)]
    private Jeu $jeu;

    #[MongoDB\ReferenceOne(targetDocument: Utilisateur::class)]
    private Utilisateur $joueur;

    #[MongoDB\Field(type: 'string')]
    private string $typePoint;

    #[MongoDB\Field(type: 'string')]
    private string $couleurType;

    #[MongoDB\Field(type: 'float')]
    private float $coordonneeX;

    #[MongoDB\Field(type: 'float')]
    private float $coordonneeY;

    #[MongoDB\Field(type: 'string')]
    private string $zoneTerrain;

    #[MongoDB\Field(type: 'string')]
    private string $frappe;

    // Getter et setters

    public function getId(): ?string 
    { 
        return $this->id; 
    }

    public function getJeu(): Jeu 
    { 
        return $this->jeu; 
    }

    public function setJeu(Jeu $jeu): void 
    { 
        $this->jeu = $jeu; 
    }

    public function getJoueur(): Utilisateur 
    { 
        return $this->joueur; 
    }

    public function setJoueur(Utilisateur $joueur): void 
    { 
        $this->joueur = $joueur; 
    }

    public function getTypePoint(): string 
    { 
        return $this->typePoint; 
    }

    public function setTypePoint(string $typePoint): void 
    { 
        $this->typePoint = $typePoint; 
    }

    public function getCouleurType(): string 
    { 
        return $this->couleurType; 
    }

    public function setCouleurType(string $couleurType): void 
    { 
        $this->couleurType = $couleurType; 
    }

    public function getCoordonneeX(): float 
    { 
        return $this->coordonneeX; 
    }

    public function setCoordonneeX(float $coordonneeX): void 
    { 
        $this->coordonneeX = $coordonneeX; 
    }

    public function getCoordonneeY(): float 
    { 
        return $this->coordonneeY; 
    }

    public function setCoordonneeY(float $coordonneeY): void 
    { 
        $this->coordonneeY = $coordonneeY; 
    }

    public function getZoneTerrain(): string 
    { 
        return $this->zoneTerrain; 
    }

    public function setZoneTerrain(string $zoneTerrain): void 
    { 
        $this->zoneTerrain = $zoneTerrain; 
    }

    public function getFrappe(): string 
    { 
        return $this->frappe; 
    }

    public function setFrappe(string $frappe): void 
    { 
        $this->frappe = $frappe; 
    }
}
?>
