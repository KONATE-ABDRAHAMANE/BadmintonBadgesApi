<?php
// src/Form/UtilisateurType.php

namespace App\Form;

use App\Document\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => !$options['is_edit'],
                'help' => $options['is_edit'] ? 'Laissez vide pour ne pas changer' : null,
                'mapped' => false,
            ]);
        
        if (!empty($options['allowed_roles'])) {
            $builder->add('roles', ChoiceType::class, [
                'choices' => array_combine($options['allowed_roles'], $options['allowed_roles']),
                'multiple' => true,
                'expanded' => true,
                'label' => 'Rôles'
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'allowed_roles' => [],
            'is_edit' => false,
        ]);
    }
}