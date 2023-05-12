<?php

namespace App\Form;

use App\Entity\Medicament;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
 

use App\Entity\Ordonnance;

class OrdonnanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('frequence')
            ->add('dose')
            ->add('date_creation'  ,DateTimeType::class, [
                'data' => new \DateTime(),
                'label' => 'Choisissez votre date de fin',
                
                'required' => true ,
                'widget' => 'single_text',
    
                'attr' => [
                    'class' => 'form-control datetimepicker-input',
                ],])
            ->add('id_Consultation')
            
            ->add('Nom_Medicament' );
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ordonnance::class,
        ]);
    }
}
