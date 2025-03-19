<?php
// Fichier: src/Controller/ConducteurController.php

namespace App\Controller;
use App\Entity\Vehicule;
use App\Entity\Conducteur;
use App\Form\ConducteurType;
use App\Repository\ConducteurRepository;
use App\Repository\EquipementVehiculeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ConducteurController extends AbstractController
{
    private $logger;
    private $entity_manager;
    private $repository;

    #[ORM\OneToMany(mappedBy: 've_conducteur', targetEntity: Vehicule::class)]
    private Collection $vehicules;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entity_manager)
    {
        $this->logger = $logger;
        $this->entity_manager = $entity_manager;
        $this->repository = $entity_manager->getRepository(Conducteur::class);
        $this->vehicules = new ArrayCollection(); // Initialisation de la collection
    }
 
    #[Route('/conducteur/lister', name: 'conducteur_lister')]
   public function lister(Request $request): Response
    {
        // obtenir la liste de tous les conducteurs triés par ordre alphabétique
        // croissant
        $liste_conducteurs = $this->repository->findAllOrderedByName();
       
        return $this->render("conducteur/lister.html.twig", [
            'liste_conducteurs' => $liste_conducteurs
        ]);
    }

   #[Route('/conducteur/supprimer/{id}', name: 'conducteur_supprimer')]
   public function supprimer($id): Response
   {
       // À partir du Repository, obtenir le conducteur grâce à son identifiant
       $conducteur = $this->repository->find($id);
       
       // Dans le cas où le conducteur n'aurait pas été trouvé, générer une exception
       if (!$conducteur) {
           throw $this->createNotFoundException('Aucun conducteur d\'identifiant ' . $id . ' n\'a été trouvé');
       }
       
       // Dissocier les véhicules associés au conducteur
       foreach ($conducteur->getVehicules() as $vehicule) {
           $vehicule->setve_conducteur(null);  // Dissocier le véhicule du conducteur
           $this->entity_manager->persist($vehicule);  // Persister la modification
       }
       
       // Maintenant, on peut supprimer le conducteur
       $this->entity_manager->remove($conducteur);
       $this->entity_manager->flush();
       
       // Rediriger vers l'affichage de la liste des conducteurs
       return $this->redirectToRoute('conducteur_lister');
   }
    #[Route('/conducteur/ajouter', name: 'conducteur_creer')]
    public function ajouter(Request $request): Response
     {
  
         $this->logger->info( 'Ajouter un conducteur' );                
        
         $message_erreur = "";
  
         // créer un conducteur dont les champs sont vides
         $conducteur = new Conducteur();
  
         // créer un formulaire qui prend en compte les données du conducteur
         $form = $this->createForm(ConducteurType::class, $conducteur);
  
         // récupération des données de la requête, notamment des
         // informations liées à la saisie d'un conducteur
         $form->handleRequest($request);
        
         // Si on vient de soumettre le formulaire et que les données
         // sont valides
         if ($form->isSubmitted() && $form->isValid()) {
  
             // Check if a driver with the same name already exists
             $conducteur_existant = $this->repository->findOneBy(['co_nom' => $conducteur->getCoNom()]);
            
             if ($conducteur_existant) {
        
                 $message_erreur = 'Il existe déjà un conducteur de même nom';
        
             } else {
                
                 // alors sauvegarder le conducteur (persist, flush)
                 $this->repository->save($conducteur, true);
  
                 // se rediriger vers l'affichage de la liste des conducteurs
                 // Attnetion on utilise le nom de la route 'conducteur_lister'
                 // et non 'conducteur/lister'
                 return $this->redirectToRoute('conducteur_lister');
             }
         }
  
             // sinon afficher la page contenant le formulaire d'ajout
         if (!empty($message_erreur)) $this->addFlash('error', $message_erreur);
            
         return $this->render('conducteur/ajouter.html.twig', [
             'form' => $form->createView(),
             'message_erreur' => $message_erreur
         ]);
     }
     /**
     * Modifier un conducteur étant donné son id
     */
    #[Route('/conducteur/modifier/{id}', name: 'conducteur_modifier')]
    public function modifier(Request $request, EntityManagerInterface $entityManager, int $id): Response
     {
  
         // à partir du Repository, obtenir le conducteur grâce à son identifiant
         $conducteur = $this->repository->find($id);
        
         // dans le cas où le conducteur n'aurait pas été trouvé, générer une exception
         if (!$conducteur) {
             throw $this->createNotFoundException('Acucun conducteur d\'identifiant ' . $id . ' n\'a été trouvé');
         }
  
         // créer le formulaire lié au conducteur
         $form = $this->createForm(ConducteurType::class, $conducteur);
  
         // récupération des données de la requête, notamment des
         // informations liées à la saisie d'un conducteur
         $form->handleRequest($request);
  
         // Si on vient de soumettre le formulaire et que les données
         // sont valies
         if ($form->isSubmitted() && $form->isValid()) {
  
             // alors sauvegarder le conducteur (persist, flush)
             $this->repository->save($conducteur, true);
  
             // se rediriger vers l'affichage de la liste des conducteurs
             // Attnetion on utilise le nom de la route 'conducteur_lister'
             // et non 'conducteur/lister'
             return $this->redirectToRoute('conducteur_lister');
         }
  
         // sinon afficher la page contenant le formulaire de modification
         return $this->render('conducteur/modifier.html.twig', [
             'form' => $form->createView(),
         ]);
  
     }
 
     // Méthode pour récupérer les véhicules associés
     public function getVehicules(): Collection
     {
         return $this->vehicules;
     }
     #[Route('/conducteur/{id}/vehicules', name: 'conducteur_vehicules')]
     public function voirVehicules(int $id, EquipementVehiculeRepository $equipementRepo): Response
     {
         $conducteur = $this->repository->find($id);
         if (!$conducteur) {
             throw $this->createNotFoundException("Conducteur non trouvé");
         }
     
         $vehicules = $conducteur->getVehicules();
     
         // Calculer le prix total des équipements du conducteur
         $equipements = $equipementRepo->findByConducteur($id);
         $prixTotal = array_reduce($equipements, fn($total, $eqVe) =>
             $total + ($eqVe->getEqVeQuantite() * $eqVe->getEqVeEquipement()->getEqPrix()), 0
         );
     
         return $this->render('conducteur/vehicules.html.twig', [
             'conducteur' => $conducteur,
             'vehicules' => $vehicules,
             'prix_total' => $prixTotal,
         ]);
     }
     

     #[Route('/conducteur/synthese', name: 'conducteur_synthese')]
public function synthese(EquipementVehiculeRepository $equipementVehiculeRepository): Response
{
    $conducteurs = $this->repository->findBy([], ['co_nom' => 'ASC']); // Trier les conducteurs
    $totalGeneralEquipements = 0;

    foreach ($conducteurs as $conducteur) {
        // Trier les véhicules du conducteur par date d'achat
        $vehicules = $conducteur->getVehicules()->toArray();
        usort($vehicules, fn($a, $b) => $a->getVeDate() <=> $b->getVeDate());

        foreach ($vehicules as $vehicule) {
            // Récupérer les équipements du véhicule
            $equipements = $equipementVehiculeRepository->findByVehicule($vehicule);
            $totalEquipements = 0;
            $detailsEquipements = [];

            foreach ($equipements as $equipementVehicule) {
                $quantite = $equipementVehicule->getEqVeQuantite();
                $prixUnitaire = $equipementVehicule->getEqVeEquipement()->getEqPrix();
                $libelle = $equipementVehicule->getEqVeEquipement()->getEqLibelle();
                $sousTotal = $quantite * $prixUnitaire;
                $totalEquipements += $sousTotal;

                $detailsEquipements[] = [
                    'libelle' => $libelle,
                    'quantite' => $quantite,
                    'prix' => $prixUnitaire,
                    'sousTotal' => $sousTotal,
                ];
            }

            // Injecter les infos préparées dans le véhicule (propriété dynamique pour la vue)
            $vehicule->totalEquipements = $totalEquipements;
            $vehicule->detailsEquipements = $detailsEquipements;

            // Ajouter au total général
            $totalGeneralEquipements += $totalEquipements;
        }

        // Injecter les véhicules triés dans le conducteur
        $conducteur->vehiculesTries = $vehicules;
    }

    return $this->render('conducteur/synthese.html.twig', [
        'conducteurs' => $conducteurs,
        'totalGeneralEquipements' => $totalGeneralEquipements,
    ]);
}

}