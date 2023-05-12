<?php

namespace App\Form;


use App\Entity\Doctor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
class DoctorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

       
      
       
        ->add('Specialite')
  
        ->add('Age')
        
            ->add('nom', TextType::class, [
                'label' => 'noù'
            ])
            ->add('cin', TextType::class, [
                'label' => 'cin'
            ])
            ->add('email', TextType::class, [
                'label' => 'email'
            ])
            ->add('prenom', TextType::class, [
                'label' => 'prenom'
            ])
            ->add('adresse', TextType::class, [
                'label' => 'adresse'
            ])
            ->add('diplome', TextType::class, [
                'label' => 'diplome'
            ])
            ->add('latitude', TextType::class, [
                'label' => 'latitude'
            ]) 
            ->add('longitude', TextType::class, [
                'label' => 'longitude'
            ])
          
            ->add('password', TextType::class, [
                'label' => 'password'
            ])
           
            
        
            
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
             ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Add'
            ]);
    }
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Doctor::class,
        ]);
    }
}
