<?php
// Fichier: src/Controller/ConducteurController.php
 
namespace App\Controller;
 
use App\Entity\Conducteur;
 
use App\Repository\ConducteurRepository;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
 
// Utilisation d'un logger pour le débogage
use Psr\Log\LoggerInterface;
 
 
class ConducteurController extends AbstractController
{
    // Logger
    private $logger;
    private $entity_manager;
    private $repository;
 
    /**
     * Constructeur auquel on passe en paramètre un logger
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $entity_manager)
    {
        $this->logger = $logger;
        $this->entity_manager = $entity_manager;
        // obtenir le Repository lié au conducteur depuis l'EntityManager
        $this->repository = $entity_manager->getRepository(Conducteur::class);
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
 
    public function supprimer( $id ): Response
    {
        // à partir du Repository, obtenir le conducteur grâce à son identifiant
        $conducteur = $this->repository->find($id);
       
        // dans le cas où le conducteur n'aurait pas été trouvé, générer une exception
        if (!$conducteur) {
            throw $this->createNotFoundException('Acucun conducteur d\'identifiant ' . $id . ' n\'a été trouvé');
        }
       
        // Suppression du conducteur
        $this->entity_manager->remove($conducteur);
        $this->entity_manager->flush();
 
        // se rediriger vers l'affichage de la liste des conducteurs
        // Attention on utilise le nom de la route 'conducteur_lister'
        // et non 'conducteur/lister'
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
}
 
?>