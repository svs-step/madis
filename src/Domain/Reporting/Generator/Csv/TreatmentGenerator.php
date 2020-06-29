<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
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

namespace App\Domain\Reporting\Generator\Csv;

use App\Domain\Registry\Calculator\Completion\ConformiteTraitementCompletion;
use App\Domain\Registry\Dictionary\ConformiteTraitementLevelDictionary;
use App\Domain\Registry\Dictionary\DelayPeriodDictionary;
use App\Domain\Registry\Dictionary\TreatmentAuthorDictionary;
use App\Domain\Registry\Dictionary\TreatmentCollectingMethodDictionary;
use App\Domain\Registry\Dictionary\TreatmentLegalBasisDictionary;
use App\Domain\Registry\Dictionary\TreatmentUltimateFateDictionary;
use App\Domain\User\Repository\Collectivity;
use App\Infrastructure\ORM\Registry\Repository\Treatment;
use Symfony\Contracts\Translation\TranslatorInterface;

class TreatmentGenerator extends AbstractGenerator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Collectivity
     */
    private $collectivityRepository;

    /**
     * @var Treatment
     */
    private $treatmentRepository;

    public function __construct(
        TranslatorInterface $translatorInterface,
        Collectivity $collectivityRepository,
        Treatment $treatmentRepository
    ) {
        $this->translator             = $translatorInterface;
        $this->collectivityRepository = $collectivityRepository;
        $this->treatmentRepository    = $treatmentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function initializeExtract(): array
    {
        $headers = array_merge(
            ['Nom'],
            $this->collectivityHeaders(),
            $this->treatmentGeneralInformationsHeaders(),
            $this->treatmentDataCategoryHeaders(),
            $this->treatmentRecipientsHeaders(),
            $this->treatmentHistoricHeaders(),
            $this->treatmentDetailsHeaders(),
            $this->treatmentSecurityHeaders(),
            $this->treatmentSpecificHeaders(),
            $this->treatmentProofHeaders()
        );
        $data = [$headers];

        /** @var \App\Domain\Registry\Model\Treatment $treatment */
        foreach ($this->treatmentRepository->findAllByActiveCollectivity() as $treatment) {
            $extract = array_merge(
                [$treatment->getName()],
                $this->initializeCollectivity($treatment->getCollectivity()),
                $this->initializeTreatmentGeneralInformations($treatment),
                $this->initializeTreatmentDataCategory($treatment),
                $this->initializeTreatmentRecipients($treatment),
                $this->initializeTreatmentHistoric($treatment),
                $this->initializeTreatmentDetails($treatment),
                $this->initializeTreatmentSecurity($treatment),
                $this->initializeTreatmentSpecific($treatment),
                $this->initializeTreatmentProof($treatment)
            );
            array_push($data, $extract);
        }

        return $data;
    }

    private function collectivityHeaders(): array
    {
        $collectivityTrans = $this->translator->trans('registry.treatment.list.collectivity');

        return [
            $collectivityTrans . ' - ' . $this->translator->trans('user.collectivity.show.name'),
            $collectivityTrans . ' - ' . $this->translator->trans('user.collectivity.show.siren'),
            $collectivityTrans . ' - ' . $this->translator->trans('user.collectivity.show.address_insee'),
        ];
    }

    private function initializeCollectivity(\App\Domain\User\Model\Collectivity $collectivity)
    {
        return [
            $collectivity->getName(),
            $collectivity->getSiren(),
            $collectivity->getAddress()->getInsee(),
        ];
    }

    private function treatmentGeneralInformationsHeaders()
    {
        return [
            $this->translator->trans('registry.treatment.show.conformite_traitement'),
            $this->translator->trans('registry.treatment.show.author'),
            $this->translator->trans('registry.treatment.show.goal'),
            $this->translator->trans('registry.treatment.show.manager'),
            $this->translator->trans('registry.treatment.show.active'),
            $this->translator->trans('registry.treatment.show.legal_basis'),
            $this->translator->trans('registry.treatment.show.legal_basis_justification'),
            $this->translator->trans('registry.treatment.show.observation'),
        ];
    }

    private function initializeTreatmentGeneralInformations(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        $conformtiteTraitement = $treatment->getConformiteTraitement();

        return [
            !\is_null($conformtiteTraitement) ? ConformiteTraitementLevelDictionary::getConformites()[ConformiteTraitementCompletion::getConformiteTraitementLevel($conformtiteTraitement)] : 'Non effectuée',
            !\is_null($treatment->getAuthor()) ? TreatmentAuthorDictionary::getAuthors()[$treatment->getAuthor()] : null,
            $treatment->getGoal(),
            $treatment->getManager(),
            $treatment->isActive() ? $this->translator->trans('label.active') : $this->translator->trans('label.inactive'),
            !\is_null($treatment->getLegalBasis()) ? TreatmentLegalBasisDictionary::getBasis()[$treatment->getLegalBasis()] : null,
            $treatment->getLegalBasisJustification(),
            $treatment->getObservation(),
        ];
    }

    private function treatmentDataCategoryHeaders()
    {
        $dataCategoryTrans = $this->translator->trans('registry.treatment.tab.data_category');

        return [
            $this->translator->trans('registry.treatment.show.data_category'),
            $dataCategoryTrans . ' - ' . $this->translator->trans('registry.treatment.show.data_category_other'),
        ];
    }

    private function initializeTreatmentDataCategory(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        return [
            implode(' - ', \iterable_to_array($treatment->getDataCategories())),
            $treatment->getDataCategoryOther(),
        ];
    }

    private function treatmentRecipientsHeaders()
    {
        $recipientsTrans = $this->translator->trans('registry.treatment.tab.recipients');

        return [
            $recipientsTrans . ' - ' . $this->translator->trans('registry.treatment.show.recipient_category'),
            $recipientsTrans . ' - ' . $this->translator->trans('registry.treatment.show.contractors'),
        ];
    }

    private function initializeTreatmentRecipients(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        return [
            $treatment->getRecipientCategory(),
            implode(' - ', \iterable_to_array($treatment->getContractors())),
        ];
    }

    private function treatmentHistoricHeaders()
    {
        $historicTrans = $this->translator->trans('registry.treatment.tab.historic');

        return [
            $historicTrans . ' - ' . $this->translator->trans('registry.treatment.show.creator'),
            $historicTrans . ' - ' . $this->translator->trans('registry.treatment.show.created_at'),
            $historicTrans . ' - ' . $this->translator->trans('registry.treatment.show.updated_at'),
        ];
    }

    private function initializeTreatmentHistoric(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        return [
            $treatment->getCreator(),
            $this->getDate($treatment->getCreatedAt()),
            $this->getDate($treatment->getUpdatedAt()),
        ];
    }

    private function treatmentDetailsHeaders()
    {
        $detailsTrans    = $this->translator->trans('registry.treatment.tab.details');
        $concernedPeople = $this->translator->trans('registry.treatment.show.concerned_people');

        return [
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_particular'),
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_particular') . ' - Commentaire',
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_user'),
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_user') . ' - Commentaire',
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_agent'),
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_agent') . ' - Commentaire',
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_elected'),
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_elected') . ' - Commentaire',
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_company'),
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_company') . ' - Commentaire',
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_partner'),
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_partner') . ' - Commentaire',
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_other'),
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_other') . ' - Commentaire',
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.estimated_concerned_people'),
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.software'),
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.paper_processing'),
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.delay') . ' - Nombre',
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.delay') . ' - Période',
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.delay') . ' - Commentaire',
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.ultimate_fate'),
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.data_origin'),
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.collecting_method'),
        ];
    }

    private function initializeTreatmentDetails(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        $yes = $this->translator->trans('label.yes');
        $no  = $this->translator->trans('label.no');

        return [
            $treatment->getConcernedPeopleParticular()->isCheck() ? $yes : $no,
            $treatment->getConcernedPeopleParticular()->getComment(),
            $treatment->getConcernedPeopleUser()->isCheck() ? $yes : $no,
            $treatment->getConcernedPeopleUser()->getComment(),
            $treatment->getConcernedPeopleAgent()->isCheck() ? $yes : $no,
            $treatment->getConcernedPeopleAgent()->getComment(),
            $treatment->getConcernedPeopleElected()->isCheck() ? $yes : $no,
            $treatment->getConcernedPeopleElected()->getComment(),
            $treatment->getConcernedPeopleCompany()->isCheck() ? $yes : $no,
            $treatment->getConcernedPeopleCompany()->getComment(),
            $treatment->getConcernedPeoplePartner()->isCheck() ? $yes : $no,
            $treatment->getConcernedPeoplePartner()->getComment(),
            $treatment->getConcernedPeopleOther()->isCheck() ? $yes : $no,
            $treatment->getConcernedPeopleOther()->getComment(),
            $treatment->getEstimatedConcernedPeople(),
            $treatment->getSoftware(),
            $treatment->isPaperProcessing() ? $this->translator->trans('label.active') : $this->translator->trans('label.inactive'),
            $treatment->getDelay()->getNumber(),
            !\is_null($treatment->getDelay()->getPeriod()) ? DelayPeriodDictionary::getPeriods()[$treatment->getDelay()->getPeriod()] : null,
            $treatment->getDelay()->getComment(),
            !\is_null($treatment->getUltimateFate()) ? TreatmentUltimateFateDictionary::getUltimateFates()[$treatment->getUltimateFate()] : null,
            $treatment->getDataOrigin(),
            !\is_null($treatment->getCollectingMethod()) ? TreatmentCollectingMethodDictionary::getMethods()[$treatment->getCollectingMethod()] : null,
        ];
    }

    private function treatmentSecurityHeaders()
    {
        $securityTrans = $this->translator->trans('registry.treatment.tab.security');

        return [
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_access_control'),
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_access_control') . ' - Commentaire',
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_tracability'),
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_tracability') . ' - Commentaire',
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_saving'),
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_saving') . ' - Commentaire',
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_update'),
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_update') . ' - Commentaire',
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_other'),
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_other') . ' - Commentaire',
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_entitled_persons'),
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_open_accounts'),
            $securityTrans . ' - ' . $this->translator->trans('registry.treatment.show.security_specificities_delivered'),
        ];
    }

    private function initializeTreatmentSecurity(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        $yes = $this->translator->trans('label.yes');
        $no  = $this->translator->trans('label.no');

        return [
            $treatment->getSecurityAccessControl()->isCheck() ? $yes : $no,
            $treatment->getSecurityAccessControl()->getComment(),
            $treatment->getSecurityTracability()->isCheck() ? $yes : $no,
            $treatment->getSecurityTracability()->getComment(),
            $treatment->getSecuritySaving()->isCheck() ? $yes : $no,
            $treatment->getSecuritySaving()->getComment(),
            $treatment->getSecurityUpdate()->isCheck() ? $yes : $no,
            $treatment->getSecurityUpdate()->getComment(),
            $treatment->getSecurityOther()->isCheck() ? $yes : $no,
            $treatment->getSecurityOther()->getComment(),
            $treatment->isSecurityEntitledPersons() ? $yes : $no,
            $treatment->isSecurityOpenAccounts() ? $yes : $no,
            $treatment->isSecuritySpecificitiesDelivered() ? $yes : $no,
        ];
    }

    private function treatmentSpecificHeaders()
    {
        $specificTrans = $this->translator->trans('registry.treatment.tab.specific');

        return [
            $specificTrans . ' - ' . $this->translator->trans('registry.treatment.show.systematic_monitoring'),
            $specificTrans . ' - ' . $this->translator->trans('registry.treatment.show.large_scale_collection'),
            $specificTrans . ' - ' . $this->translator->trans('registry.treatment.show.vulnerable_people'),
            $specificTrans . ' - ' . $this->translator->trans('registry.treatment.show.data_crossing'),
            $specificTrans . ' - ' . $this->translator->trans('registry.treatment.show.evaluation_or_rating'),
            $specificTrans . ' - ' . $this->translator->trans('registry.treatment.show.automated_decisions_with_legal_effect'),
            $specificTrans . ' - ' . $this->translator->trans('registry.treatment.show.automatic_exclusion_service'),
            $specificTrans . ' - ' . $this->translator->trans('registry.treatment.show.innovative_use'),
        ];
    }

    private function initializeTreatmentSpecific(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        $yes = $this->translator->trans('label.yes');
        $no  = $this->translator->trans('label.no');

        return [
            $treatment->isSystematicMonitoring() ? $yes : $no,
            $treatment->isLargeScaleCollection() ? $yes : $no,
            $treatment->isVulnerablePeople() ? $yes : $no,
            $treatment->isDataCrossing() ? $yes : $no,
            $treatment->isEvaluationOrRating() ? $yes : $no,
            $treatment->isAutomatedDecisionsWithLegalEffect() ? $yes : $no,
            $treatment->isAutomaticExclusionService() ? $yes : $no,
            $treatment->isInnovativeUse() ? $yes : $no,
        ];
    }

    private function treatmentProofHeaders()
    {
        return [
            'Nb ' . $this->translator->trans('label.linked_documents'),
        ];
    }

    private function initializeTreatmentProof(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        return [
            count($treatment->getProofs()),
        ];
    }
}
