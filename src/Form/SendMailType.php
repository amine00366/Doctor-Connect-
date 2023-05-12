<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use FOS\CKEditorBundle\Form\Type\CKEditorType;


class SendMailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('subject',TextType::class,[
                'attr'=>[
                    'placeholder'=>' your subject'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your subject',
                    ]),
                ]
            ])
            ->add('message',CKEditorType::class,[
                'attr'=>[
                    'placeholder'=>' ecrivez votre message '
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your message',
                    ]),
                ]


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