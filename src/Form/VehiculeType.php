<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\Vehicule;
use App\Entity\Conducteur;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ve_marque', TextType::class, [
                'label' => 'Marque',
                'required' => true,
            ])
            ->add('ve_modele', TextType::class, [
                'label' => 'Modèle',
                'required' => true,
            ])
            ->add('ve_conducteur', EntityType::class, [
                'class' => Conducteur::class,
                'choice_label' => 'co_nom',
                'label' => 'Conducteur',
                'placeholder' => 'Sélectionner un conducteur',
                'autocomplete' => true, // Active la recherche dynamique
                'attr' => ['class' => 'conducteur-select'], // Classe CSS pour intégration JS
            ])
            ->add('ve_date', DateType::class, [
                'label' => 'Date d\'acquisition',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('vehicules_conducteur', ChoiceType::class, [
                'label' => 'Véhicules du conducteur',
                'mapped' => false,
                'choices' => [], // Rempli dynamiquement par AJAX
                'attr' => ['class' => 'vehicules-liste'],
            ])
            ->add('prix_total_equipements', HiddenType::class, [
                'mapped' => false,
                'attr' => ['class' => 'prix-total-equipements'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}

?>