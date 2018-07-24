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

namespace App\Domain\Maturity\Form\Type;

use App\Domain\Maturity\Model;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerType extends AbstractType
{
    private $options = [
        'label'   => false,
        'choices' => [
            'Nul'   => 1,
            'Moyen' => 2,
            'Bon'   => 3,
        ],
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('response', ChoiceType::class, $this->options)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $builder
                ->remove('response');
        }, 300);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Model\Answer::class,
                'validation_groups' => [
                    'default',
                    'answer',
                ],
            ]);
    }
}
