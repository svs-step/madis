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

use App\Domain\Registry\Model\Proof;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProofType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'registry.proof.form.name',
                'required' => true,
            ])
            ->add('type', DictionaryType::class, [
                'label'    => 'registry.proof.form.type',
                'name'     => 'registry_proof_type',
                'required' => true,
            ])
            ->add('documentFile', FileType::class, [
                'label'    => false,
                'required' => false,
            ])
            ->add('comment', TextType::class, [
                'label'    => 'registry.proof.form.comment',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Proof::class,
                'validation_groups' => [
                    'default',
                    'proof',
                ],
            ]);
    }
}
