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

namespace App\Domain\Admin\Cloner;

use App\Domain\Registry\Model as RegistryModel;
use App\Domain\User\Model as UserModel;

class TreatmentCloner extends AbstractCloner
{
    /**
     * {@inheritdoc}
     *
     * @param RegistryModel\Treatment $referent
     *
     * @throws \Exception
     */
    protected function cloneReferentForCollectivity($referent, UserModel\Collectivity $collectivity): RegistryModel\Treatment
    {
        $treatment = new RegistryModel\Treatment();

        $treatment->setName($referent->getName());
        $treatment->setGoal($referent->getGoal());
        $treatment->setManager($referent->getManager());
        $treatment->setSoftware($referent->getSoftware());
        $treatment->setTools($referent->getTools());
        $treatment->setPaperProcessing($referent->isPaperProcessing());
        $treatment->setLegalBasis($referent->getLegalBasis());
        $treatment->setLegalBasisJustification($referent->getLegalBasisJustification());
        $treatment->setObservation($referent->getObservation());
        if (null !== $referent->getConcernedPeopleParticular()) {
            $treatment->setConcernedPeopleParticular($referent->getConcernedPeopleParticular());
        }
        if (null !== $referent->getConcernedPeopleUser()) {
            $treatment->setConcernedPeopleUser($referent->getConcernedPeopleUser());
        }
        if (null !== $referent->getConcernedPeopleAgent()) {
            $treatment->setConcernedPeopleAgent($referent->getConcernedPeopleAgent());
        }
        if (null !== $referent->getConcernedPeopleElected()) {
            $treatment->setConcernedPeopleElected($referent->getConcernedPeopleElected());
        }
        if (null !== $referent->getConcernedPeopleCompany()) {
            $treatment->setConcernedPeopleCompany($referent->getConcernedPeopleCompany());
        }
        if (null !== $referent->getConcernedPeoplePartner()) {
            $treatment->setConcernedPeoplePartner($referent->getConcernedPeoplePartner());
        }
        if (null !== $referent->getConcernedPeopleUsager()) {
            $treatment->setConcernedPeopleUsager($referent->getConcernedPeopleUsager());
        }
        if (null !== $referent->getConcernedPeopleOther()) {
            $treatment->setConcernedPeopleOther($referent->getConcernedPeopleOther());
        }
        $treatment->setDataCategories($referent->getDataCategories());
        $treatment->setDataCategoryOther($referent->getDataCategoryOther());
        $treatment->setDataOrigin($referent->getDataOrigin());
        $treatment->setRecipientCategory($referent->getRecipientCategory());
        if (null !== $referent->getDelay()) {
            $treatment->setDelay($referent->getDelay());
        }
        if (null !== $referent->getSecurityAccessControl()) {
            $treatment->setSecurityAccessControl($referent->getSecurityAccessControl());
        }
        if (null !== $referent->getSecurityTracability()) {
            $treatment->setSecurityTracability($referent->getSecurityTracability());
        }
        if (null !== $referent->getSecuritySaving()) {
            $treatment->setSecuritySaving($referent->getSecuritySaving());
        }
        if (null !== $referent->getSecurityUpdate()) {
            $treatment->setSecurityUpdate($referent->getSecurityUpdate());
        }
        if (null !== $referent->getSecurityOther()) {
            $treatment->setSecurityOther($referent->getSecurityOther());
        }
        $treatment->setSystematicMonitoring($referent->isSystematicMonitoring());
        $treatment->setLargeScaleCollection($referent->isLargeScaleCollection());
        $treatment->setVulnerablePeople($referent->isVulnerablePeople());
        $treatment->setDataCrossing($referent->isDataCrossing());
        $treatment->setEvaluationOrRating($referent->isEvaluationOrRating());
        $treatment->setAutomatedDecisionsWithLegalEffect($referent->isAutomatedDecisionsWithLegalEffect());
        $treatment->setAutomaticExclusionService($referent->isAutomaticExclusionService());
        $treatment->setActive($referent->isActive());
        $treatment->setSecurityEntitledPersons($referent->isSecurityEntitledPersons());
        $treatment->setSecurityOpenAccounts($referent->isSecurityOpenAccounts());
        $treatment->setSecuritySpecificitiesDelivered($referent->isSecuritySpecificitiesDelivered());
        $treatment->setAuthor($referent->getAuthor());
        $treatment->setCollectingMethod($referent->getCollectingMethod());
        $treatment->setEstimatedConcernedPeople($referent->getEstimatedConcernedPeople());
        $treatment->setUltimateFate($referent->getUltimateFate());
        $treatment->setLegalMentions($referent->getLegalMentions());
        $treatment->setConsentRequest($referent->getConsentRequest());
        $treatment->setConsentRequestFormat($referent->getConsentRequestFormat());

        $treatment->setCollectivity($collectivity);
        $treatment->setClonedFrom($referent);

        return $treatment;
    }
}
