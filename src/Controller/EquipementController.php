<?php
// src/Controller/EquipementController.php
namespace App\Controller;
 
use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
 
// Utilisation d'un logger pour le débogage
use Psr\Log\LoggerInterface;
 
class EquipementController extends AbstractController
{
 
    // Logger
    private $logger;
    private $entity_manager;
    private $repository;
   
    /**
     * Constructeur
     */
    public function __construct(LoggerInterface $logger,
        EntityManagerInterface $entity_manager)
    {
        $this->logger = $logger;
        $this->entity_manager = $entity_manager;
        // obtenir le Repository lié au conducteur depuis l'EntityManager
        $this->repository = $entity_manager->getRepository(Equipement::class);
    }

         // equipement normalement
         #[Route('/equipement/lister', name: 'equipement_lister')]
         public function lister(Request $request): Response
          {
              $liste_equipements = $equipements_repository->findAllOrdered();
       
              return $this->render("equipement/lister.html.twig", [
                  'liste_equipements' => $liste_equipements
              ]);
          }
          /**
     * Supprimer un équipement étant donné son id
     */
    #[Route('/equipement/supprimer/{id}', name: 'equipement_supprimer')]
    public function supprimer( $id ): Response
     {
         // Récupérer l'équipement par son id
         $equipement = $this->repository->find($id);
  
         if (!$equipement) {
             throw $this->createNotFoundException('Aucun équipement avec l\'identifiant ' . $id . ' n\'a été trouvé');
         }
  
         // Suppression du equipement
         $this->entity_manager->remove($equipement);
         $this->entity_manager->flush();
  
         return $this->redirectToRoute('equipement_lister');
     }
  
     /**
      * Supprimer tous les équipements (debug/test)
      */
     #[Route('/equipement/supprimer_tout', name: 'equipement_supprimer_tout')]
    public function supprimer_tout(): Response
     {
         // Récupérer les equipements
         $equipements = $this->repository->findAll();
  
         foreach($equipements as $equipement) {
             $this->entity_manager->remove($equipement);
         }
        
         $this->entity_manager->flush();
  
         return $this->redirectToRoute('equipement_lister');
     }
  
     // ...
 
   
}
 
?>