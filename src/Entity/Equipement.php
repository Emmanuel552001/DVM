<?php
// Equipement.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\EquipementRepository;
use App\Entity\EquipementVehicule;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    // --------------------------------------------------------------
    // Description des champs
    // --------------------------------------------------------------
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $eq_id = null;

    #[ORM\Column(type:'string', length:30)]
    private ?string $eq_libelle = null;

    #[ORM\Column(type:'float')]
    private ?float $eq_prix = null;

    // --------------------------------------------------------------
    // Propriété pour la relation inverse avec EquipementVehicule
    // --------------------------------------------------------------
    #[ORM\OneToMany(mappedBy: 'eqve_equipement', targetEntity: EquipementVehicule::class)]
    private Collection $eq_equipement_vehicule;

    // --------------------------------------------------------------
    // Constructeur
    // --------------------------------------------------------------
    public function __construct()
    {
        $this->eq_equipement_vehicule = new ArrayCollection(); // Initialisation de la collection
    }

    // --------------------------------------------------------------
    // Méthodes
    // --------------------------------------------------------------
    public function getEqId() {
        return $this->eq_id;
    }

    public function getEqLibelle() {
        return $this->eq_libelle;
    }

    public function setEqLibelle($libelle) {
        $this->eq_libelle = $libelle;
        return $this;
    }

    public function getEqPrix() {
        return $this->eq_prix;
    }

    public function setEqPrix($prix) {
        $this->eq_prix = $prix;
        return $this;
    }

    // Méthodes pour gérer les EquipementVehicule
    public function getEqEquipementVehicule(): Collection
    {
        return $this->eq_equipement_vehicule;
    }

    public function addEqEquipementVehicule(EquipementVehicule $equipement_vehicule): static
    {
        if (!$this->eq_equipement_vehicule->contains($equipement_vehicule)) {
            $this->eq_equipement_vehicule[] = $equipement_vehicule;
            $equipement_vehicule->setEqVeEquipement($this);
        }

        return $this;
    }

    public function removeEqEquipementVehicule(EquipementVehicule $equipement_vehicule): static
    {
        if ($this->eq_equipement_vehicule->removeElement($equipement_vehicule)) {
            if ($equipement_vehicule->getEqVeEquipement() === $this) {
                $equipement_vehicule->setEqVeEquipement(null);
            }
        }

        return $this;
    }
}

?>
