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

    #[Route('/equipement/lister', name: 'equipement_lister')]
    public function lister(Request $request): Response
    {
        // Utilisation de $this->repository pour récupérer les équipements
        $liste_equipements = $this->repository->findAll(); // Ou bien si tu veux utiliser une méthode personnalisée : findAllOrdered()
       
        return $this->render("equipement/lister.html.twig", [
            'liste_equipements' => $liste_equipements
        ]);
    }
    

          /**
     * Supprimer un équipement étant donné son id
     */
/**
 * Supprimer un équipement étant donné son id
 */
#[Route('/equipement/supprimer/{id}', name: 'equipement_supprimer')]
public function supprimer($id): Response
{
    // Récupérer l'équipement par son id
    $equipement = $this->repository->find($id);

    if (!$equipement) {
        throw $this->createNotFoundException('Aucun équipement avec l\'identifiant ' . $id . ' n\'a été trouvé');
    }

    // Supprimer les relations dans la table equipement_vehicule
    $connection = $this->entity_manager->getConnection();
    $connection->executeQuery('DELETE FROM equipement_vehicule WHERE eqve_equipement_id = ?', [$id]);

    // Supprimer l'équipement
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
 
    /**
     * Créer un nouvel équipement en affichant un formulaire
     * de saisie des informations
     */
    #[Route('/equipement/ajouter', name: 'equipement_ajouter')]
   public function ajouter(Request $request): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
 
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->save($equipement, true);
 
            return $this->redirectToRoute('equipement_lister');
        }
 
        return $this->render('equipement/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
 
    // ...
     /**
    * Modifier un équipement étant donné son id
   */
  #[Route('/equipement/modifier/{id}', name: 'equipement_modifier')]
  public function modifier(Request $request, int $id): Response
    {
         $equipement = $this->repository->find($id);
       
        if (!$equipement) {
            throw $this->createNotFoundException('Aucun équipement avec l\'identifiant ' . $id . ' n\'a été trouvé');
        }
 
        $form = $this->createForm(EquipementType::class, $equipement);
 
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
           
            $this->repository->save($equipement, true);
 
            return $this->redirectToRoute('equipement_lister');
        }
 
        return $this->render('equipement/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
 
    }

}

?>