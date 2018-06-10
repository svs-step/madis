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

use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class CollectivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'user.collectivity.form.name',
                'required' => true,
            ])
            ->add('shortName', TextType::class, [
                'label'    => 'user.collectivity.form.short_name',
                'required' => true,
            ])
            ->add('type', DictionaryType::class, [
                'label'    => 'user.collectivity.form.type',
                'required' => true,
                'name'     => 'user_collectivity_type',
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('siren', NumberType::class, [
                'label'    => 'user.collectivity.form.siren',
                'required' => true,
            ])
            ->add('active', ChoiceType::class, [
                'label'    => 'user.collectivity.form.active',
                'required' => true,
                'choices'  => [
                    'label.active'   => true,
                    'label.inactive' => false,
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('website', UrlType::class, [
                'label'    => 'user.collectivity.form.website',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'https://',
                ],
            ])
        ;
    }
}
