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

use App\Domain\Registry\Model\Embeddable\Delay;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DelayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', IntegerType::class, [
                'label'    => false,
                'required' => false,
                'attr'     => [
                    'min' => 1,
                ],
            ])
            ->add('period', DictionaryType::class, [
                'label'    => false,
                'name'     => 'registry_delay_period',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Delay::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
