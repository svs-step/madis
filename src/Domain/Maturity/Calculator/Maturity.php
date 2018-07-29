<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Maturity\Calculator;

use App\Domain\Maturity\Model;

class Maturity
{
    /**
     * @param Model\Survey $survey
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
     * @param Model\Maturity[] $maturityList
     *
     * @return int
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
