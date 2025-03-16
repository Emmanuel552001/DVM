<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\VehiculeRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $ve_id = null;

    #[ORM\Column(type:'string', length:30)]
    #[Assert\NotBlank(message: "La marque ne peut pas être vide.")]
    private ?string $ve_marque = null;

    #[ORM\Column(type:'string', length:30)]
    #[Assert\NotBlank(message: "Le modèle ne peut pas être vide.")]
    private ?string $ve_modele = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private $ve_date;

    #[ORM\ManyToOne(targetEntity: Conducteur::class, inversedBy: "vehicules")]
    #[ORM\JoinColumn(nullable: true, name: 've_co_id', referencedColumnName: 'co_id')]
    private ?Conducteur $ve_conducteur = null;

    #[ORM\OneToMany(mappedBy: 'eqve_vehicule', targetEntity: EquipementVehicule::class, cascade: ['persist', 'remove'])]
    private Collection $ve_equipement_vehicule;

    public function __construct()
    {
        $this->ve_date = new \DateTime();
        $this->ve_equipement_vehicule = new ArrayCollection(); // Initialisation de la collection
    }

    public function getVeId(): ?int
    {
        return $this->ve_id;
    }

    public function getVeMarque(): ?string
    {
        return $this->ve_marque;
    }

    public function setVeMarque(string $ve_marque): self
    {
        $this->ve_marque = $ve_marque;
        return $this;
    }

    public function getVeModele(): ?string
    {
        return $this->ve_modele;
    }

    public function setVeModele(string $ve_modele): self
    {
        $this->ve_modele = $ve_modele;
        return $this;
    }

    public function getVeDate(): ?\DateTimeInterface
    {
        return $this->ve_date;
    }

    public function setVeDate(\DateTimeInterface $ve_date): self
    {
        $this->ve_date = $ve_date;
        return $this;
    }

    public function getVeConducteur(): ?Conducteur
    {
        return $this->ve_conducteur;
    }

    public function setVeConducteur(?Conducteur $ve_conducteur): self
    {
        $this->ve_conducteur = $ve_conducteur;
        return $this;
    }

    public function getVeEquipementVehicule(): Collection
    {
        return $this->ve_equipement_vehicule;
    }

    public function addVeEquipementVehicule(EquipementVehicule $equipement_vehicule): self
    {
        if (!$this->ve_equipement_vehicule->contains($equipement_vehicule)) {
            $this->ve_equipement_vehicule[] = $equipement_vehicule;
            $equipement_vehicule->setEqVeVehicule($this);
        }

        return $this;
    }

    public function removeVeEquipementVehicule(EquipementVehicule $equipement_vehicule): self
    {
        if ($this->ve_equipement_vehicule->removeElement($equipement_vehicule)) {
            if ($equipement_vehicule->getEqVeVehicule() === $this) {
                $equipement_vehicule->setEqVeVehicule(null);
            }
        }

        return $this;
    }
}



?>