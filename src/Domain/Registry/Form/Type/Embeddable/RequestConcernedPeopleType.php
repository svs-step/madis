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

use App\Domain\Registry\Model\Embeddable\RequestConcernedPeople;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestConcernedPeopleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', DictionaryType::class, [
                'label'    => 'registry.request_concerned_people.form.civility',
                'required' => false,
                'name'     => 'registry_request_civility',
            ])
            ->add('firstName', TextType::class, [
                'label'    => 'registry.request_concerned_people.form.first_name',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'registry.request_concerned_people.form.last_name',
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'label'    => 'registry.request_concerned_people.form.address',
                'required' => false,
            ])
            ->add('mail', EmailType::class, [
                'label'    => 'registry.request_concerned_people.form.mail',
                'required' => false,
            ])
            ->add('phoneNumber', TextType::class, [
                'label'    => 'registry.request_concerned_people.form.phone_number',
                'required' => false,
            ])
            ->add('linkWithApplicant', TextType::class, [
                'label'    => 'registry.request_concerned_people.form.link_with_applicant',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => RequestConcernedPeople::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
