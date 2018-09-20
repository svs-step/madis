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

namespace App\Domain\Registry\Form\Type;

use App\Domain\Registry\Model\Violation;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ViolationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'label'    => 'registry.violation.form.date',
                'required' => true,
            ])
            ->add('inProgress', CheckboxType::class, [
                'label'    => 'registry.violation.form.in_progress',
                'required' => false,
            ])
            ->add('violationNature', DictionaryType::class, [
                'label'    => 'registry.violation.form.violation_nature',
                'name'     => 'registry_violation_nature',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('origins', DictionaryType::class, [
                'label'    => 'registry.violation.form.origins',
                'name'     => 'registry_violation_origin',
                'expanded' => false,
                'multiple' => true,
            ])
            ->add('cause', DictionaryType::class, [
                'label'    => 'registry.violation.form.cause',
                'name'     => 'registry_violation_cause',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('concernedDataNature', DictionaryType::class, [
                'label'    => 'registry.violation.form.concerned_data_nature',
                'name'     => 'registry_violation_concerned_data',
                'expanded' => false,
                'multiple' => true,
            ])
            ->add('concernedPeopleCategories', DictionaryType::class, [
                'label'    => 'registry.violation.form.concerned_people_categories',
                'name'     => 'registry_violation_concerned_people',
                'expanded' => false,
                'multiple' => true,
            ])
            ->add('nbAffectedRows', IntegerType::class, [
                'label' => 'registry.violation.form.nb_affected_rows',
                'attr'  => [
                    'min' => 0,
                ],
            ])
            ->add('nbAffectedPersons', IntegerType::class, [
                'label' => 'registry.violation.form.nb_affected_persons',
                'attr'  => [
                    'min' => 0,
                ],
            ])
            ->add('potentialImpactsNature', DictionaryType::class, [
                'label'    => 'registry.violation.form.potential_impacts_nature',
                'name'     => 'registry_violation_impact',
                'expanded' => false,
                'multiple' => true,
            ])
            ->add('gravity', DictionaryType::class, [
                'label'    => 'registry.violation.form.gravity',
                'name'     => 'registry_violation_gravity',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('communication', DictionaryType::class, [
                'label'    => 'registry.violation.form.communication',
                'name'     => 'registry_violation_communication',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('communicationPrecision', TextareaType::class, [
                'label'    => 'registry.violation.form.communication_precision',
                'required' => false,
                'attr'     => [
                    'rows' => 5,
                ],
            ])
            ->add('appliedMeasuresAfterViolation', TextareaType::class, [
                'label' => 'registry.violation.form.applied_measures_after_violation',
                'attr'  => [
                    'rows' => 5,
                ],
            ])
            ->add('notification', DictionaryType::class, [
                'label'       => 'registry.violation.form.notification',
                'name'        => 'registry_violation_notification',
                'required'    => false,
                'expanded'    => true,
                'multiple'    => false,
                'placeholder' => 'Aucune notification à envoyer',
            ])
            ->add('notificationDetails', TextType::class, [
                'label'    => 'registry.violation.form.notification_details',
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label'    => 'registry.violation.form.comment',
                'required' => false,
                'attr'     => [
                    'rows' => 5,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Violation::class,
                'validation_groups' => [
                    'default',
                    'violation',
                ],
            ]);
    }
}
