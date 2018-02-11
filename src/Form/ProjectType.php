<?php
namespace App\Form;

use App\Entity\Projet;
use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('tags', EntityType::class, array(
                'class' => Tag::class,
                'choice_label' => function ($tags) {
                    return $tags->getName();
                },
                'multiple' => true
            ))
            ->add('description', TextareaType::class)
            ->add('url', TextType::class)
            ->add('image', FileType::class, array(
                'data_class' => null
            ))
            ->add('priority', IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Projet::class,
        ));
    }
}