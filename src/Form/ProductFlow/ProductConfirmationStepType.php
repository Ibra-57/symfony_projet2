<?php

namespace App\Form\ProductFlow;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class ProductConfirmationStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('confirmation', CheckboxType::class, [
                'label' => 'Je confirme que ce produit de plus de 500€ est correctement configuré',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez confirmer pour continuer.',
                    ]),
                ],
                'attr' => ['class' => 'rounded']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
