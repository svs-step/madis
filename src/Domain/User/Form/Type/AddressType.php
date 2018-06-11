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

namespace App\Domain\User\Form\Type;

use App\Domain\User\Model\Embeddable\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lineOne', TextType::class, [
                'label'    => 'user.address.form.line_one',
                'required' => true,
            ])
            ->add('lineTwo', TextType::class, [
                'label'    => 'user.address.form.line_two',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label'    => 'user.address.form.city',
                'required' => true,
            ])
            ->add('zipCode', NumberType::class, [
                'label'    => 'user.address.form.zip_code',
                'required' => true,
            ])
            ->add('insee', TextType::class, [
                'label'    => 'user.address.form.insee',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Address::class,
                'validation_groups' => 'default',
            ]);
    }
}
