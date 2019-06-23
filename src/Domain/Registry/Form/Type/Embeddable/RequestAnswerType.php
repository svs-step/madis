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

use App\Domain\Registry\Model\Embeddable\RequestAnswer;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestAnswerType extends AbstractType
{
    /**
     * Build type form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('response', TextareaType::class, [
                'label'    => 'registry.request_answer.form.response',
                'required' => false,
                'attr'     => [
                    'rows' => 4,
                ],
            ])
            ->add('date', DateType::class, [
                'label'    => 'registry.request_answer.form.date',
                'required' => false,
            ])
            ->add('type', DictionaryType::class, [
                'label'    => 'registry.request_answer.form.type',
                'name'     => 'registry_request_answer_type',
                'required' => false,
            ])
        ;
    }

    /**
     * Provide type options.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => RequestAnswer::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
