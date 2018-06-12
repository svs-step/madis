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

use App\Domain\User\Model\Embeddable\Contact;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $intersectIsEmpty = empty(\array_intersect(
            [
                'collectivity_legal_manager',
                'collectivity_referent',
            ],
            $options['validation_groups'] ?? []
        ));

        $required = $intersectIsEmpty ? false : true;

        $builder
            ->add('civility', DictionaryType::class, [
                'label'    => 'user.contact.form.civility',
                'required' => $required,
                'name'     => 'user_contact_civility',
            ])
            ->add('firstName', TextType::class, [
                'label'    => 'user.contact.form.first_name',
                'required' => $required,
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'user.contact.form.last_name',
                'required' => $required,
            ])
            ->add('job', TextType::class, [
                'label'    => 'user.contact.form.job',
                'required' => $required,
            ])
            ->add('mail', EmailType::class, [
                'label'    => 'user.contact.form.mail',
                'required' => $required,
            ])
            ->add('phoneNumber', TextType::class, [
                'label'    => 'user.contact.form.phone_number',
                'required' => $required,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Contact::class,
                'validation_groups' => 'default',
            ]);
    }
}
