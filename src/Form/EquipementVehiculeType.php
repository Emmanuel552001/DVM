<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Equipement;
use App\Entity\Vehicule;
use App\Entity\EquipementVehicule;

class EquipementVehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eqve_equipement', EntityType::class, [
                'class' => Equipement::class,
                'choice_label' => 'eq_libelle',
                'label' => 'Équipement',
                'placeholder' => 'Sélectionner un équipement',
                'required' => true
            ])
            ->add('eqve_vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choice_label' => function (Vehicule $vehicule) {
                    return $vehicule->getVeMarque() . ' - ' . $vehicule->getVeModele();
                },
                'label' => 'Véhicule',
                'placeholder' => 'Sélectionner un véhicule',
                'required' => true
            ])
            ->add('eqve_quantite', IntegerType::class, [
                'label' => 'Quantité',
                'required' => true,
                'attr' => ['min' => 1]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EquipementVehicule::class,
        ]);
    }
}
?>