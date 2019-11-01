<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Departement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('mail')
            ->add('message')
            ->add('departements', EntityType::class, [
                       'class' => Departement::class,
                       'choice_label' => 'nom',
                       'choice_value' => function (Departement $entity = null) {
                          return $entity ? $entity->getNom() : '';
},
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
