<?php
namespace App\Repository;
 
use App\Entity\EquipementVehicule;
use App\Entity\Equipement;
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
 
   
    public function save(Vehicule $vehicule, bool $flush = false): void
    {
        $this->getEntityManager()->persist($vehicule);
 
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function findByConducteur(int $conducteurId): array
{
    return $this->createQueryBuilder('ev')
        ->join('ev.eqve_vehicule', 'v')
        ->join('v.ve_conducteur', 'c')
        ->where('c.co_id = :conducteurId')
        ->setParameter('conducteurId', $conducteurId)
        ->getQuery()
        ->getResult();
}
    
public function findByVehicule(Vehicule $vehicule): array
{
    return $this->createQueryBuilder('ev')
        ->where('ev.eqve_vehicule = :vehicule')
        ->setParameter('vehicule', $vehicule)
        ->getQuery()
        ->getResult();
}

}
 

?>