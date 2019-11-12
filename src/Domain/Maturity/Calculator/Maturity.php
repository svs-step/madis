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

namespace App\Domain\Maturity\Calculator;

use App\Domain\Maturity\Model;

class Maturity
{
    /**
     * Compute maturity indice for each linked survey maturity.
     *
     * @param Model\Survey $survey The survey maturities object to compute
     *
     * @throws \Exception
     *
     * @return Model\Maturity[]
     */
    public function generateMaturityByDomain(Model\Survey $survey): array
    {
        $domains      = [];
        $points       = [];
        $maturityList = [];

        // Get all existant maturity to update it
        foreach ($survey->getMaturity() as $item) {
            $maturityList[$item->getDomain()->getId()->toString()] = $item;
        }

        // Begin calculation
        foreach ($survey->getAnswers() as $answer) {
            $domain   = $answer->getQuestion()->getDomain();
            $domainId = $domain->getId()->toString();

            // Get all domain in specific array
            if (!isset($domains[$domainId])) {
                $domains[$domainId] = $domain;
            }

            // Make an addition with answer response by domain
            if (isset($points[$domainId])) {
                $points[$domainId] += $answer->getResponse();
            } else {
                $points[$domainId] = $answer->getResponse();
            }
        }

        // Update maturityList with new points
        // If maturity doesn't exists for related domain, create it
        foreach ($points as $key => $point) {
            if (!isset($maturityList[$key])) {
                $maturityList[$key] = new Model\Maturity();
                $maturityList[$key]->setDomain($domains[$key]);
                $maturityList[$key]->setSurvey($survey);
            }
            // * 10 to keep int data in order to display a {int}/10 in report
            $maturityList[$key]->setScore(\intval(\ceil($point / 14 * 5 * 10)));
        }

        return $maturityList;
    }

    /**
     * Get the global score of every maturity provided as parameter
     * Make an average of maturity indice of each Maturity.
     *
     * @param Model\Maturity[] $maturityList
     */
    public function getGlobalScore(array $maturityList = []): int
    {
        $points = 0;
        foreach ($maturityList as $maturity) {
            $points += $maturity->getScore();
        }

        return \intval(\ceil($points / \count($maturityList)));
    }
}
