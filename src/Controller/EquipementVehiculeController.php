<?php

namespace App\Controller;

use App\Entity\EquipementVehicule;
use App\Form\EquipementVehiculeType;
use App\Repository\EquipementVehiculeRepository;
use App\Entity\Vehicule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/equipementvehicule')]
class EquipementVehiculeController extends AbstractController
{
    #[Route('/ajouter/{id}', name: 'ajouter_equipement_vehicule')]
    public function ajouter(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $equipementVehicule = new EquipementVehicule();

        // Pré-remplir le véhicule si besoin
        $vehicule = $em->getRepository(Vehicule::class)->find($id);
        if (!$vehicule) {
            throw $this->createNotFoundException('Véhicule non trouvé');
        }
        $equipementVehicule->setEqVeVehicule($vehicule);

        $form = $this->createForm(EquipementVehiculeType::class, $equipementVehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($equipementVehicule);
            $em->flush();

            return $this->redirectToRoute('vehicule_detail', ['id' => $id]);
        }

        return $this->render('equipementVehicule/ajouter.html.twig', [
            'form' => $form->createView(),
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/modifier/{id}', name: 'modifier_equipement_vehicule')]
    public function modifier(int $id, Request $request, EntityManagerInterface $em, EquipementVehiculeRepository $repository): Response
    {
        $equipementVehicule = $repository->find($id);
        if (!$equipementVehicule) {
            throw $this->createNotFoundException('Association équipement-véhicule non trouvée');
        }

        $form = $this->createForm(EquipementVehiculeType::class, $equipementVehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('vehicule_detail', [
                'id' => $equipementVehicule->getEqVeVehicule()->getVeId()
            ]);
        }

        return $this->render('equipementVehicule/modifier.html.twig', [
            'form' => $form->createView(),
            'equipementVehicule' => $equipementVehicule,
        ]);
    }

    #[Route('/supprimer/{id}', name: 'supprimer_equipement_vehicule')]
    public function supprimer(int $id, EntityManagerInterface $em, EquipementVehiculeRepository $repository): Response
    {
        $equipementVehicule = $repository->find($id);
        if (!$equipementVehicule) {
            throw $this->createNotFoundException('Association équipement-véhicule non trouvée');
        }

        $id = $equipementVehicule->getEqVeVehicule()->getVeId();

        $em->remove($equipementVehicule);
        $em->flush();

        return $this->redirectToRoute('vehicule_detail', ['id' => $id]);
    }
}
