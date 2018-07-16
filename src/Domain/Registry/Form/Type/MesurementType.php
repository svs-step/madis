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

namespace App\Domain\Registry\Form\Type;

use App\Domain\Registry\Model\Mesurement;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MesurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'registry.mesurement.form.name',
                'required' => true,
            ])
            /*
            ->add('type', DictionaryType::class, [
                'label'    => 'registry.mesurement.form.type',
                'name'     => 'registry_mesurement_type',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            */
            ->add('description', TextareaType::class, [
                'label'    => 'registry.mesurement.form.description',
                'required' => false,
                'attr'     => [
                    'rows' => 3,
                ],
            ])
            ->add('cost', TextType::class, [
                'label'    => 'registry.mesurement.form.cost',
                'required' => false,
            ])
            ->add('charge', TextType::class, [
                'label'    => 'registry.mesurement.form.charge',
                'required' => false,
            ])
            ->add('status', DictionaryType::class, [
                'label'    => 'registry.mesurement.form.status',
                'name'     => 'registry_mesurement_status',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('planificationDate', DateType::class, [
                'label'             => 'registry.mesurement.form.planification_date',
                'required'          => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Mesurement::class,
                'validation_groups' => [
                    'default',
                    'mesurement',
                ],
            ]);
    }
}
