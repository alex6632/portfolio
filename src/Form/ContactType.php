<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, array(
                'attr' => array('placeholder' => 'Votre nom'),
                'constraints' => array(
                    new NotBlank(array("message" => "Votre nom est obligatoire")),
                )
            ))
            ->add('firstName', TextType::class, array(
                'attr' => array('placeholder' => 'Votre prénom'),
                'constraints' => array(
                    new NotBlank(array("message" => "Votre prénom est obligatoire")),
                )
            ))
            ->add('email', EmailType::class, array(
                'attr' => array('placeholder' => 'Votre email'),
                'constraints' => array(
                    new NotBlank(array("message" => "Votre email est obligatoire")),
                    new Email(array("message" => "Votre email est invalide"))
                )
            ))
            ->add('message', TextareaType::class, array(
                'attr' => array('placeholder' => 'Entrez votre message ici'),
                'constraints' => array(
                    new NotBlank(array("message" => "Vous auriez pas oublié un petit quelque chose ? ;-)")),
                    new Length(array('min' => 10))
                )
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Envoyer'
            ));
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true
        ));
    }

    public function getName()
    {
        return 'contact_form';
    }
}