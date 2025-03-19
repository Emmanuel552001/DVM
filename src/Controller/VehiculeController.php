<?php
 
namespace App\Controller;
 
use App\Entity\Vehicule;
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


     #[Route("/conducteur/{id}/equipements/prix_total", name: "conducteur_equipements_prix_total", methods: ["GET"])]
public function getPrixTotalEquipements(int $id, EquipementVehiculeRepository $repo): JsonResponse
{
    $equipements = $repo->findByConducteur($id);
    $prixTotal = array_reduce($equipements, fn($total, $eqVe) => $total + ($eqVe->getEqVeQuantite() * $eqVe->getEqVeEquipement()->getEqPrix()), 0);
    return $this->json(['prix_total' => $prixTotal]);
}


#[Route('/vehicule/detail/{id}', name: 'vehicule_detail')]
public function afficherDetail($id, VehiculeRepository $vehiculeRepository): Response
{
    $vehicule = $vehiculeRepository->find($id);

    if (!$vehicule) {
        throw $this->createNotFoundException('Véhicule non trouvé');
    }

    return $this->render('vehicule/detail.html.twig', [
        'vehicule' => $vehicule,
        'equipementsVehicule' => $vehicule->getVeEquipementVehicule()
    ]);
}
/*
#[Route('/equipementVehicule/ajouter/{id}', name: 'ajouter_equipement_vehicule')]
public function ajouter_equipement_vehicule(Request $request, int $id): Response
{
    // Récupérer le véhicule par son ID
    $vehicule = $this->getDoctrine()->getRepository(Vehicule::class)->find($id);

    // Vérifier si le véhicule existe
    if (!$vehicule) {
        throw $this->createNotFoundException('Véhicule non trouvé');
    }

    // Créer un nouvel objet EquipementVehicule
    $equipementVehicule = new EquipementVehicule();

    // Créer et traiter le formulaire pour l'ajout de l'équipement
    $form = $this->createForm(EquipementVehiculeType::class, $equipementVehicule);
    $form->handleRequest($request);

    // Si le formulaire est soumis et valide, ajouter l'équipement au véhicule
    if ($form->isSubmitted() && $form->isValid()) {
        // Ajouter l'équipement au véhicule
        $vehicule->addVeEquipementVehicule($equipementVehicule);

        // Sauvegarder l'équipement et la relation avec le véhicule
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($equipementVehicule);
        $entityManager->flush();

        // Rediriger vers la page de détail du véhicule ou une autre page
        return $this->redirectToRoute('detail_vehicule', ['id' => $id]);
    }

    // Rendre la vue avec le formulaire
    return $this->render('equipement_vehicule/ajouter_equipement_vehicule.html.twig', [
        'form' => $form->createView(),
    ]);
}

     #[Route('/equipementVehicule/modifier/{id}', name: 'modifier_equipement_vehicule')]
     public function modifier_equipement_vehicule(Request $request, int $id): Response
     {
         // Récupérer l'équipement véhicule par son ID
         $equipementVehicule = $this->getDoctrine()->getRepository(EquipementVehicule::class)->find($id);
     
         // Vérifier si l'équipement existe
         if (!$equipementVehicule) {
             throw $this->createNotFoundException('Équipement de véhicule non trouvé');
         }
     
         // Créer et traiter le formulaire
         $form = $this->createForm(EquipementVehiculeType::class, $equipementVehicule);
         $form->handleRequest($request);
     
         // Si le formulaire est soumis et valide, enregistrer les modifications
         if ($form->isSubmitted() && $form->isValid()) {
             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->flush();
     
             // Rediriger l'utilisateur après la modification
             return $this->redirectToRoute('equipement_vehicule_liste');
         }
     
         // Rendre la vue avec le formulaire
         return $this->render('equipement_vehicule/modifier_equipement_vehicule.html.twig', [
             'form' => $form->createView(),
         ]);
     }
     
#[Route('/equipementVehicule/supprimer/{id}', name: 'supprimer_equipement_vehicule')]
public function supprimer_equipement_vehicule(Request $request, int $id): Response
     {}


*/
    
}