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

namespace App\Infrastructure\ORM\Registry\Repository\ConformiteTraitement;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;

class Question extends CRUDRepository implements Repository\ConformiteTraitement\Question
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\ConformiteTraitement\Question::class;
    }

    public function findNewQuestionsNotUseInGivenConformite(Model\ConformiteTraitement\ConformiteTraitement $conformiteTraitement)
    {
        $qb = $this->createQueryBuilder();

        $params = array_map(function (Model\ConformiteTraitement\Reponse $reponse) {
            return $reponse->getQuestion()->getId()->toString();
        }, \iterable_to_array($conformiteTraitement->getReponses()))
        ;

        if (count($params)) {
            $qb->andWhere($qb->expr()->notIn('o.id', ':questions'))
                ->setParameter(
                    'questions',
                    $params
                )
            ;
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
