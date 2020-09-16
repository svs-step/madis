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

namespace App\Tests\Domain\Registry\Form\Type\ConformiteTraitement;

use App\Domain\Registry\Form\Type\ConformiteTraitement\ConformiteTraitementType;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Treatment;
use App\Tests\Utils\FormTypeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConformiteTraitementTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new ConformiteTraitementType());
    }

    public function testBuildForm()
    {
        $builder = [
            'reponses'   => CollectionType::class,
            'traitement' => TreatmentType::class,
        ];

        $conformiteTraitement = new ConformiteTraitement();
        $conformiteTraitement->setTraitement(new Treatment());

        (new ConformiteTraitementType())->buildForm($this->prophesizeBuilder($builder), ['data' => $conformiteTraitement]);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => ConformiteTraitement::class,
            'validation_groups' => [
                'default',
                'conformite_traitement',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new ConformiteTraitementType())->configureOptions($resolverProphecy->reveal());
    }
}
