<?php
namespace App\Repository;

use App\Entity\EquipementVehicule;
use App\Entity\Vehicule;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipementVehicule>
 */
class EquipementVehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipementVehicule::class);
    }

    /**
     * Méthode pour rechercher les EquipementVehicule par véhicule
     * 
     * @param Vehicule $vehicule
     * @return EquipementVehicule[] Retourne une liste d'EquipementVehicule
     */
    public function findByVehicule(Vehicule $vehicule)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.eqve_vehicule = :vehicule') // Utilise eqve_vehicule, pas "vehicule"
            ->setParameter('vehicule', $vehicule)
            ->getQuery()
            ->getResult();
    }

    public function save(Vehicule $vehicule, bool $flush = false): void
    {
        $this->getEntityManager()->persist($vehicule);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
 
?>