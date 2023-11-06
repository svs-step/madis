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

namespace App\Domain\Registry\Model\ConformiteTraitement;

use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\TreatmentDataCategory;
use App\Domain\Reporting\Model\LoggableSubject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ConformiteTraitement implements LoggableSubject
{
    use CreatorTrait;
    use HistoryTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Treatment
     */
    private $traitement;

    /**
     * @var iterable|Reponse[]
     */
    private $reponses;

    /**
     * @var int
     */
    private $nbConformes;

    /**
     * @var int
     */
    private $nbNonConformesMineures;

    /**
     * @var int
     */
    private $nbNonConformesMajeures;

    /**
     * @var array|AnalyseImpact[]
     */
    private $analyseImpacts;

    /**
     * @var string|null
     */
    private $updatedBy;

    public function __construct()
    {
        $this->id                     = Uuid::uuid4();
        $this->reponses               = [];
        $this->nbConformes            = 0;
        $this->nbNonConformesMineures = 0;
        $this->nbNonConformesMajeures = 0;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTraitement(): Treatment
    {
        return $this->traitement;
    }

    public function setTraitement(Treatment $traitement): void
    {
        $this->traitement = $traitement;
    }

    /**
     * @return iterable|Reponse[]
     */
    public function getReponses(): iterable
    {
        return $this->reponses;
    }

    public function getReponseOfName(string $name): ?Reponse
    {
        foreach ($this->reponses as $reponse) {
            if ($reponse->getQuestion()->getQuestion() === $name) {
                return $reponse;
            }
        }

        return null;
    }

    public function addReponse(Reponse $reponse): void
    {
        $this->reponses[] = $reponse;
        $reponse->setConformiteTraitement($this);
    }

    public function removeReponse(Reponse $reponse): void
    {
        $key = \array_search($reponse, $this->reponses, true);

        if (false === $key) {
            return;
        }

        unset($this->reponses[$key]);
    }

    public function getNbConformes(): int
    {
        return $this->nbConformes;
    }

    public function setNbConformes(int $nbConformes): void
    {
        $this->nbConformes = $nbConformes;
    }

    public function getNbNonConformesMineures(): int
    {
        return $this->nbNonConformesMineures;
    }

    public function setNbNonConformesMineures(int $nbNonConformesMineures): void
    {
        $this->nbNonConformesMineures = $nbNonConformesMineures;
    }

    public function getNbNonConformesMajeures(): int
    {
        return $this->nbNonConformesMajeures;
    }

    public function setNbNonConformesMajeures(int $nbNonConformesMajeures): void
    {
        $this->nbNonConformesMajeures = $nbNonConformesMajeures;
    }

    public function getAnalyseImpacts()
    {
        return $this->analyseImpacts;
    }

    public function getLastAnalyseImpact(): ?AnalyseImpact
    {
        /** @var AnalyseImpact|null $return */
        $return = null;
        foreach ($this->analyseImpacts as $analyseImpact) {
            if (null === $return || $return->getDateValidation() < $analyseImpact->getDateValidation()) {
                $return = $analyseImpact;
            }
        }

        return $return;
    }

    public function setAnalyseImpacts($analyseImpacts): void
    {
        $this->analyseImpacts = $analyseImpacts;
    }

    public function __toString(): string
    {
        return $this->traitement->__toString();
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * This returns true if the current treatment needs an AIPD to be done.
     */
    public function getNeedsAipd(): bool
    {
        $treatment = $this->getTraitement();

        if (count($this->getAnalyseImpacts()) > 0) {
            // Already has AIPD
            return false;
        }
        if ($treatment->isExemptAIPD()) {
            // Treatment is exempted
            return false;
        }
        if (
            $treatment->isSystematicMonitoring()
            || $treatment->isLargeScaleCollection()
            || $treatment->isVulnerablePeople()
            || $treatment->isDataCrossing()
            || $treatment->isEvaluationOrRating()
            || $treatment->isAutomatedDecisionsWithLegalEffect()
            || $treatment->isAutomaticExclusionService()
            || $treatment->isInnovativeUse()
        ) {
            // If one of these items is true, check if there are sensitive data categories
            /** @var TreatmentDataCategory $cat */
            foreach ($treatment->getDataCategories() as $cat) {
                if ($cat->isSensible()) {
                    return true;
                }
            }
        }

        return false;
    }
}
