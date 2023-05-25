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

namespace App\Domain\Registry\Calculator\Completion;

use App\Domain\Registry\Model;

class TreatmentCompletion extends AbstractCompletion
{
    /**
     * {@inheritdoc}
     * Get completion points for Treatment.
     *
     * @param Model\Treatment $object
     */
    protected function getPoints($object): int
    {
        $points = 0;

        if ($object->getName()) {
            ++$points;
        }

        if ($object->getGoal()) {
            ++$points;
        }

        if ($object->getManager()) {
            ++$points;
        }

        if (($object->getTools() && count($object->getTools())) || $object->isPaperProcessing()) {
            ++$points;
        }

        if ($object->getLegalBasis()) {
            ++$points;
        }

        if ($object->getLegalBasisJustification()) {
            ++$points;
        }

        if ($object->getConcernedPeopleParticular()->isCheck() || $object->getConcernedPeopleUser()->isCheck()
            || $object->getConcernedPeopleAgent()->isCheck() || $object->getConcernedPeopleElected()->isCheck()
            || $object->getConcernedPeopleCompany()->isCheck() || $object->getConcernedPeoplePartner()->isCheck()
            || $object->getConcernedPeopleOther()->isCheck()
        ) {
            ++$points;
        }

        if (!empty($object->getDataCategories())) {
            ++$points;
        }

        if ($object->getDataCategoryOther()) {
            ++$points;
        }

        if ($object->getRecipientCategory()) {
            ++$points;
        }

        if (!empty($object->getContractors())) {
            ++$points;
        }

        if ($object->getDelay()->getNumber() || $object->getDelay()->getComment()) {
            ++$points;
        }

        return $points;
    }

    protected function getMaxPoints(): int
    {
        return 12;
    }
}
