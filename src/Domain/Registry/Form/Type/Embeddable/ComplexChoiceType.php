<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Form\Type\Embeddable;

use App\Domain\Registry\Model\Embeddable\ComplexChoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComplexChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('check', CheckboxType::class, [
                'label'      => false,
                'required'   => false,
                'label_attr' => [
                    'removeColClass' => true,
                ],
            ])
            ->add('comment', TextType::class, [
                'label'    => false,
                'required' => false,
                'attr'     => [
                    'placeholder' => 'placeholder.precision',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => ComplexChoice::class,
                'validation_groups' => [
                    'default',
                ],
            ])
        ;
    }
}
