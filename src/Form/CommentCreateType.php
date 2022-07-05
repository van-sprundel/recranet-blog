<?php

namespace App\Form;

use App\Entity\BlogPost;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CommentCreateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('body', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Post a comment'
                ]
            ])
            ->add('post', SubmitType::class, [

                'attr' => [
                    'class' => 'bg-button text-button-text py-2 px-5 mt-2 rounded'
                ],
            ]);

    }
}