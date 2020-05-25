<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Domain\Registry\Form\Type\ConformiteTraitement;

use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ReponseType extends AbstractType
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
        $builder
            ->add('conforme', ChoiceType::class, [
                'label'   => false,
                'choices' => [
                    'Non conforme' => false,
                    'Conforme'     => true,
                ],
                'attr' => [
                    'class' => 'conformite-select',
                ],
            ])
            ->add('actionProtections', EntityType::class, [
                'required'      => false,
                'label'         => false,
                'class'         => Mesurement::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var User $user */
                    $user = $this->security->getUser();

                    return $er->createQueryBuilder('m')
                        ->andWhere('m.collectivity = :collectivity')
                        ->setParameter('collectivity', $user->getCollectivity())
                        ->orderBy('m.name', 'ASC');
                },
                'choice_label' => 'name',
                'expanded'     => false,
                'multiple'     => true,
                'attr'         => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
                ],
            ]
            )
        ;
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Reponse::class,
                'validation_groups' => [
                    'default',
                    'reponse',
                ],
            ]);
    }
}
