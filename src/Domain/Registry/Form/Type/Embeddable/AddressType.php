<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Form\Type\Embeddable;

use App\Domain\Registry\Model\Embeddable\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = \in_array('contractor', $options['validation_groups']);

        $builder
            ->add('lineOne', TextType::class, [
                'label'    => 'registry.address.form.line_one',
                'required' => $required,
            ])
            ->add('lineTwo', TextType::class, [
                'label'    => 'registry.address.form.line_two',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label'    => 'registry.address.form.city',
                'required' => $required,
            ])
            ->add('zipCode', NumberType::class, [
                'label'    => 'registry.address.form.zip_code',
                'required' => $required,
            ])
            ->add('mail', EmailType::class, [
                'label'    => 'registry.address.form.mail',
                'required' => $required,
            ])
            ->add('phoneNumber', TextType::class, [
                'label'    => 'registry.address.form.phone_number',
                'required' => $required,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Address::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
