<?php
// Définition de l'espace de nom
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ConducteurRepository;

#[ORM\Entity(repositoryClass: ConducteurRepository::class)]
class Conducteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $CoId = null;

    #[ORM\Column(type:'string', length:30)]
    private ?string $CoNom = null;

    // Relation OneToMany avec l'entité Vehicule
    #[ORM\OneToMany(mappedBy: 've_conducteur', targetEntity: Vehicule::class)]
    private Collection $vehicules;

    public function __construct()
    {
        $this->vehicules = new ArrayCollection();
    }

    // Getter pour obtenir la collection de véhicules
    public function getVehicules(): Collection
    {
        return $this->vehicules;
    }

    // Setter pour la collection de véhicules (si vous souhaitez la remplacer entièrement)
    public function setVehicules(Collection $vehicules): self
    {
        $this->vehicules = $vehicules;
        return $this;
    }

    // Méthode pour ajouter un véhicule
    public function addVehicule(Vehicule $vehicule): self
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules[] = $vehicule;
            $vehicule->setVeConducteur($this); // Assurer la relation bidirectionnelle
        }

        return $this;
    }

    // Méthode pour retirer un véhicule
    public function removeVehicule(Vehicule $vehicule): self
    {
        if ($this->vehicules->removeElement($vehicule)) {
            // Assurer la relation bidirectionnelle
            if ($vehicule->getVeConducteur() === $this) {
                $vehicule->setVeConducteur(null);
            }
        }

        return $this;
    }

    // Getter pour l'identifiant du conducteur
    public function getCoId(): ?int
    {
        return $this->CoId;
    }

    // Getter pour le nom du conducteur
    public function getCoNom(): ?string
    {
        return $this->CoNom;
    }

    // Setter pour le nom du conducteur
    public function setCoNom(string $nom): self
    {
        $this->CoNom = $nom;
        return $this;
    }
}

 
?>