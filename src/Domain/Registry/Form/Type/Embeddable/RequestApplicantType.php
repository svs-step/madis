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

use App\Domain\Registry\Model\Embeddable\RequestApplicant;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestApplicantType extends AbstractType
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
            ->add('civility', DictionaryType::class, [
                'label'    => 'registry.request_applicant.form.civility',
                'required' => true,
                'name'     => 'registry_request_civility',
            ])
            ->add('firstName', TextType::class, [
                'label'    => 'registry.request_applicant.form.first_name',
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'registry.request_applicant.form.last_name',
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'label'    => 'registry.request_applicant.form.address',
                'required' => false,
            ])
            ->add('mail', EmailType::class, [
                'label'    => 'registry.request_applicant.form.mail',
                'required' => false,
            ])
            ->add('phoneNumber', TextType::class, [
                'label'    => 'registry.request_applicant.form.phone_number',
                'required' => false,
            ])
            ->add('concernedPeople', CheckboxType::class, [
                'label'    => 'registry.request_applicant.form.concerned_people',
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
                'data_class'        => RequestApplicant::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
