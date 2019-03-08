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

namespace App\Domain\Registry\Calculator\Completion;

use App\Domain\Registry\Model;

class TreatmentCompletion extends AbstractCompletion
{
    /**
     * Get completion points for Treatment.
     *
     * @param Model\Treatment $object
     *
     * @return int
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

        if ($object->getSoftware() || $object->isPaperProcessing()) {
            ++$points;
        }

        if ($object->getLegalBasis()) {
            ++$points;
        }

        if ($object->getLegalBasisJustification()) {
            ++$points;
        }

        if (!empty($object->getConcernedPeople())) {
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

    /**
     * Get max number point possible.
     *
     * @return int
     */
    protected function getMaxPoints(): int
    {
        return 12;
    }
}
