<?php

namespace App\Form;

use Doctrine\Common\Collections\Expr\Value;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TraiterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'disabled' => true,
                'attr' => [
                    'class'=> 'form-control', 
                    'Value'=> 'Traiter cette reclamation',
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'votre e-mail',
                'attr' => [
                    'class' => 'form-control'
                ]
                ])
            ->add('message', CKEditorType::class, [
               'label' => 'votre msg'
                
           ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
