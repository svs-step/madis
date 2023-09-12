<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Form\Type;

use App\Application\Form\Extension\SanitizeTextFormType;
use App\Domain\Registry\Form\Type\Embeddable\ComplexChoiceType;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Tool;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ToolType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $this->security->getUser();

        /** @var Tool $tool */
        $tool = $options['data'] ?? null;
        $builder
            ->add('name', SanitizeTextFormType::class, [
                'label'    => 'registry.tool.form.name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])

            ->add('type', DictionaryType::class, [
                'label'    => 'registry.tool.form.type',
                'name'     => 'registry_tool_type',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'registry.tool.form.description',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('other_info', TextareaType::class, [
                'label'    => 'registry.tool.form.other_info',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('editor', SanitizeTextFormType::class, [
                'label'    => 'registry.tool.form.editor',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('manager', SanitizeTextFormType::class, [
                'label'    => 'registry.tool.form.manager',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])

            ->add('contractors', EntityType::class, [
                'label'         => 'registry.tool.form.contractors',
                'class'         => Contractor::class,
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'query_builder' => function (EntityRepository $er) use ($tool) {
                    /** @var User $authenticatedUser */
                    $authenticatedUser = $this->security->getUser();
                    $collectivity      = $tool ? $tool->getCollectivity() : $authenticatedUser->getCollectivity();
                    $qb                = $er->createQueryBuilder('c');
                    $qb->addOrderBy('c.name', 'asc');
                    if ($collectivity && $collectivity->getIsServicesEnabled()) {
                        $ors = $qb->expr()->orX();

                        if (count($authenticatedUser->getServices()) > 0) {
                            foreach ($authenticatedUser->getServices() as $service) {
                                $ors->add($qb->expr()->eq('c.service', $service));
                            }
                            // Services enabled on user collectivity, check if that contractor belongs to this service
                            $qb->andWhere($qb->expr()->orX($ors));
                        }
                    } else {
                        $qb->andWhere('c.collectivity = :collectivity')
                            ->setParameter(':collectivity', $collectivity)
                        ;
                    }

                    return $qb;
                },
                'attr' => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'placeholder.multiple_select',
                    'aria-label'       => 'Sous-traitants',
                ],
            ])

            ->add('prod_date', DateType::class, [
                'label'    => 'registry.tool.form.prod_date',
                'required' => false,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'html5'    => false,
                'attr'     => [
                    'class' => 'datepicker',
                ],
            ])

            ->add('country_type', ChoiceType::class, [
                'label'    => 'registry.tool.form.country_type',
                'choices'  => Tool::COUNTRY_TYPES,
                'required' => false,
            ])

            ->add('country_name', SanitizeTextFormType::class, [
                'label'    => 'registry.tool.form.country_name',
                'required' => false,
            ])

            ->add('country_guarantees', SanitizeTextFormType::class, [
                'label'    => 'registry.tool.form.country_guarantees',
                'required' => true,
            ])

            ->add('archival', ComplexChoiceType::class, [
                'label'    => 'registry.tool.form.archival',
                'required' => false,
            ])
            ->add('encrypted', ComplexChoiceType::class, [
                'label'    => 'registry.tool.form.encrypted',
                'required' => false,
            ])
            ->add('access_control', ComplexChoiceType::class, [
                'label'    => 'registry.tool.form.access_control',
                'required' => false,
            ])
            ->add('update', ComplexChoiceType::class, [
                'label'    => 'registry.tool.form.update',
                'required' => false,
            ])
            ->add('backup', ComplexChoiceType::class, [
                'label'    => 'registry.tool.form.backup',
                'required' => false,
            ])
            ->add('deletion', ComplexChoiceType::class, [
                'label'    => 'registry.tool.form.deletion',
                'required' => false,
            ])
            ->add('tracking', ComplexChoiceType::class, [
                'label'    => 'registry.tool.form.tracking',
                'required' => false,
            ])
            ->add('has_comment', ComplexChoiceType::class, [
                'label'    => 'registry.tool.form.has_comment',
                'required' => false,
            ])
            ->add('other', ComplexChoiceType::class, [
                'label'    => 'registry.tool.form.other',
                'required' => false,
            ])
            ->add('updatedBy', HiddenType::class, [
                'required' => false,
                'data'     => $user ? $user->getFirstName() . ' ' . strtoupper($user->getLastName()) : '',
            ])
        ;
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Tool::class,
                'validation_groups' => [
                    'default',
                    'tool',
                ],
            ]);
    }
}
