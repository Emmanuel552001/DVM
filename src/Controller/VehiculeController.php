<?php
 
namespace App\Controller;
 
use App\Entity\Vehicule;
use App\Entity\EquipementVehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use App\Repository\ConducteurRepository;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
 
// Utilisation d'un logger pour le débogage
use Psr\Log\LoggerInterface;
 
class VehiculeController extends AbstractController
{
    // Logger
    private $logger;
    private $entity_manager;
    private $repository;
    private $vehicule_repository;
    /**
     * Constructeur auquel on passe en paramètre un logger
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $entity_manager, VehiculeRepository $vehiculeRepository)
    {
        $this->logger = $logger;
        $this->entity_manager = $entity_manager;
        // obtenir le Repository lié au véhicule depuis l'EntityManager
        $this->repository = $entity_manager->getRepository(Vehicule::class);
        $this->vehicule_repository = $vehiculeRepository;
    }
   
#[Route('/vehicule/lister', name: 'vehicule_lister')]
   public function lister(Request $request): Response
    {
        $liste_vehicules = $this->vehicule_repository->findAllOrdered();
 
        return $this->render("vehicule/lister.html.twig", [
            'liste_vehicules' => $liste_vehicules
        ]);
    }
     /**
     * Supprimer un véhicule étant donné son id
     */
    #[Route('/vehicule/supprimer/{id}', name: 'vehicule_supprimer')]
    public function supprimer( $id ): Response
     {
         // Récupérer le vehicule par son id
         $vehicule = $this->repository->find($id);
  
         if (!$vehicule) {
             throw $this->createNotFoundException('Aucun véhicule avec l\'identifiant ' . $id . ' n\'a été trouvé');
         }
  
         // Suppression du vehicule
         $this->entity_manager->remove($vehicule);
         $this->entity_manager->flush();
  
         return $this->redirectToRoute('vehicule_lister');
     }
  
     /**
      * Supprimer tous les véhicules (debug/test)
      */
     #[Route('/vehicule/supprimer_tout', name: 'vehicule_supprimer_tout')]
    public function supprimer_tout(): Response
     {
         // Récupérer les vehicules
         $vehicules = $this->repository->findAll();
  
         foreach($vehicules as $vehicule) {
             $this->entity_manager->remove($vehicule);
         }
        
         $this->entity_manager->flush();
  
         return $this->redirectToRoute('vehicule_lister');
     }
  
     // ...
     /**
     * Ajout d'un nouveau véhicule
     */
    #[Route('/vehicule/ajouter', name: 'vehicule_ajouter')]
    public function ajouter(Request $request): Response
     {
         $vehicule = new Vehicule();
         $form = $this->createForm(VehiculeType::class, $vehicule);
  
         $form->handleRequest($request);
  
         if ($form->isSubmitted() && $form->isValid()) {
             $this->repository->save($vehicule, true);
  
             return $this->redirectToRoute('vehicule_lister');
         }
  
         return $this->render('vehicule/ajouter.html.twig', [
             'form' => $form->createView(),
         ]);
     }
     /**
     * Modifier un véhicule étant donné son id
     */
    #[Route('/vehicule/modifier/{id}', name: 'vehicule_modifier')]
    public function modifier(Request $request, int $id): Response
     {
  
         $vehicule = $this->repository->find($id);
        
        
         if (!$vehicule) {
             throw $this->createNotFoundException('Aucun véhicule avec l\'identifiant ' . $id . ' n\'a été trouvé');
         }
  
         $form = $this->createForm(VehiculeType::class, $vehicule);
  
         $form->handleRequest($request);
  
         if ($form->isSubmitted() && $form->isValid()) {
            
             $this->repository->save($vehicule, true);
  
             return $this->redirectToRoute('vehicule_lister');
         }
  
         return $this->render('vehicule/modifier.html.twig', [
             'form' => $form->createView(),
         ]);
  
     }
     /**
 * @Route("/vehicules/conducteur/{id}", name="vehicules_par_conducteur", methods={"GET"})
 */
public function getVehiculesParConducteur($id, VehiculeRepository $vehiculeRepository): JsonResponse
{
    $vehicules = $vehiculeRepository->findBy(['ve_co_id' => $id]);
    $data = [];

    foreach ($vehicules as $vehicule) {
        $data[] = [
            'id' => $vehicule->getVeId(),
            'marque' => $vehicule->getVeMarque(),
            'modele' => $vehicule->getVeModele(),
        ];
    }

    return $this->json(['vehicules' => $data]);
}
/*
#[Route('/equipement/ajouter/{vehiculeId}', name: 'equipement_ajouter')]
public function ajouterEquipement(Request $request, int $vehiculeId): Response
{
    $vehicule = $this->entity_manager->getRepository(Vehicule::class)->find($vehiculeId);
    if (!$vehicule) {
        throw $this->createNotFoundException("Véhicule non trouvé");
    }

    $equipementVehicule = new EquipementVehicule();
    $equipementVehicule->setEqVeVehicule($vehicule);

    $form = $this->createForm(EquipementVehiculeType::class, $equipementVehicule);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->entity_manager->persist($equipementVehicule);
        $this->entity_manager->flush();

        return $this->redirectToRoute('vehicule_lister');
    }

    return $this->render('equipement/ajouter.html.twig', [
        'form' => $form->createView(),
    ]);
}*/

#[Route('/equipement/modifier/{id}', name: 'equipement_modifier')]
public function modifierEquipement(Request $request, int $id): Response
{
    $equipementVehicule = $this->entity_manager->getRepository(EquipementVehicule::class)->find($id);
    if (!$equipementVehicule) {
        throw $this->createNotFoundException("Équipement non trouvé");
    }

    $form = $this->createForm(EquipementVehiculeType::class, $equipementVehicule);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->entity_manager->flush();

        return $this->redirectToRoute('vehicule_lister');
    }

    return $this->render('equipement/modifier.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/equipement/supprimer/{id}', name: 'equipement_supprimer', methods: ['DELETE'])]
public function supprimerEquipement(int $id): JsonResponse
{
    $equipementVehicule = $this->entity_manager->getRepository(EquipementVehicule::class)->find($id);
    if (!$equipementVehicule) {
        return $this->json(['error' => 'Équipement non trouvé'], Response::HTTP_NOT_FOUND);
    }

    $this->entity_manager->remove($equipementVehicule);
    $this->entity_manager->flush();

    return $this->json(['success' => 'Équipement supprimé']);
}


    
}
?>