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

use App\Domain\Registry\Form\Type\Embeddable\ComplexChoiceType;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Tool;
use App\Domain\User\Model\Service;
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
            ->add('name', TextType::class, [
                'label'    => 'registry.tool.label.name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])

            ->add('type', DictionaryType::class, [
                'label'    => 'registry.tool.label.type',
                'name'     => 'registry_tool_type',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'registry.tool.label.description',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('other_info', TextareaType::class, [
                'label'    => 'registry.tool.label.other_info',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('editor', TextType::class, [
                'label'    => 'registry.tool.label.editor',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('manager', TextType::class, [
                'label'    => 'registry.tool.label.manager',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])

            ->add('contractors', EntityType::class, [
                'label'         => 'global.label.linked_contractor',
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
                        // Services enabled on user collectivity, check if that contractor belongs to this service
                        $ors = $qb->expr()->orX();

                        if (count($authenticatedUser->getServices()) > 0) {
                            /** @var Service $service */
                            foreach ($authenticatedUser->getServices() as $i => $service) {
                                $ors->add($qb->expr()->eq('c.service', ':service' . $i));
                                $qb->setParameter(':service' . $i, $service);
                            }
                            $qb->andWhere($ors);
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
                    'title'            => 'global.placeholder.multiple_select',
                    'aria-label'       => 'Sous-traitants',
                ],
            ])

            ->add('prod_date', DateType::class, [
                'label'    => 'registry.tool.label.prod_date',
                'required' => false,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'html5'    => false,
                'attr'     => [
                    'class' => 'datepicker',
                ],
            ])

            ->add('country_type', ChoiceType::class, [
                'label'    => 'registry.tool.label.country_type',
                'choices'  => Tool::COUNTRY_TYPES,
                'required' => false,
            ])

            ->add('country_name', TextType::class, [
                'label'       => 'registry.tool.label.country_name',
                'required'    => false,
                'purify_html' => true,
            ])

            ->add('country_guarantees', TextType::class, [
                'label'       => 'registry.tool.label.country_guarantees',
                'required'    => true,
                'purify_html' => true,
            ])

            ->add('archival', ComplexChoiceType::class, [
                'label'    => 'registry.tool.label.archival',
                'required' => false,
            ])
            ->add('encrypted', ComplexChoiceType::class, [
                'label'    => 'registry.tool.label.encrypted',
                'required' => false,
            ])
            ->add('access_control', ComplexChoiceType::class, [
                'label'    => 'registry.tool.label.access_control',
                'required' => false,
            ])
            ->add('update', ComplexChoiceType::class, [
                'label'    => 'registry.tool.label.update',
                'required' => false,
            ])
            ->add('backup', ComplexChoiceType::class, [
                'label'    => 'registry.tool.label.backup',
                'required' => false,
            ])
            ->add('deletion', ComplexChoiceType::class, [
                'label'    => 'registry.tool.label.deletion',
                'required' => false,
            ])
            ->add('tracking', ComplexChoiceType::class, [
                'label'    => 'registry.tool.label.tracking',
                'required' => false,
            ])
            ->add('has_comment', ComplexChoiceType::class, [
                'label'    => 'registry.tool.label.has_comment',
                'required' => false,
            ])
            ->add('other', ComplexChoiceType::class, [
                'label'    => 'registry.tool.label.other',
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
