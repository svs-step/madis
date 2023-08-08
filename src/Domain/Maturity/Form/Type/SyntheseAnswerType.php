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

namespace App\Domain\Maturity\Form\Type;

use App\Domain\Maturity\Model;
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SyntheseAnswerType extends AbstractType
{
    protected Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $parentForm   = $event->getForm()->getParent()->getParent();
            $collectivity = $parentForm->getData()->getCollectivity();
            $event->getForm()->add('mesurements', EntityType::class, [
                    'required'      => false,
                    'label'         => false,
                    'class'         => Mesurement::class,
                    'query_builder' => function (EntityRepository $er) use ($collectivity) {
                        /** @var User $user */
                        $user = $this->security->getUser();

                        return $er->createQueryBuilder('m')
                            ->andWhere('m.collectivity = :collectivity')
                            ->setParameter('collectivity', $collectivity)
                            ->andWhere('m.status = :nonApplied')
                            ->setParameter('nonApplied', MesurementStatusDictionary::STATUS_NOT_APPLIED)
                            ->orderBy('m.name', 'ASC');
                    },
                    'choice_label'  => 'name',
                    'expanded'      => false,
                    'multiple'      => true,
                    'attr'          => [
                        'class'            => 'selectpicker',
                        'title'            => 'placeholder.multiple_select_action_protection',
                        'data-live-search' => true,
                    ],
                    'choice_attr'   => function (Mesurement $choice) {
                        $name = $choice->getName();
                        if (\mb_strlen($name) > 85) {
                            $name = \mb_substr($name, 0, 85) . '...';
                        }

                        return ['data-content' => $name];
                    },
                ]
            );
        });
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Model\AnswerSurvey::class,
                'validation_groups' => [
                    'default',
                    'answer',
                ],
            ]);
    }
}
