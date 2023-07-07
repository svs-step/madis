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
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Dictionary\TreatmentAuthorDictionary;
use App\Domain\Registry\Dictionary\TreatmentCollectingMethodDictionary;
use App\Domain\Registry\Dictionary\TreatmentLegalBasisDictionary;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\User\Repository\Collectivity;
use App\Infrastructure\ORM\Registry\Repository\ConformiteTraitement\Question;
use App\Infrastructure\ORM\Registry\Repository\Treatment;
use Symfony\Component\Security\Core\Security;
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

    /**
     * @var Question
     */
    private $questionRepository;

    /**
     * @var Security
     */
    private $security;

    public function __construct(
        TranslatorInterface $translatorInterface,
        Collectivity $collectivityRepository,
        Treatment $treatmentRepository,
        Question $questionRepository,
        Security $security
    ) {
        $this->translator             = $translatorInterface;
        $this->collectivityRepository = $collectivityRepository;
        $this->treatmentRepository    = $treatmentRepository;
        $this->questionRepository     = $questionRepository;
        $this->security               = $security;
    }

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
            $this->treatmentProofHeaders(),
            $this->treatmentConformiteHeaders(),
        );
        $data = [$headers];

        $user = null;
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser();
        }

        /** @var \App\Domain\Registry\Model\Treatment $treatment */
        foreach ($this->treatmentRepository->findAllByActiveCollectivity(true, $user) as $treatment) {
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
                $this->initializeTreatmentProof($treatment),
                $this->initializeTreatmentConformite($treatment),
            );
            array_push($data, $extract);
        }
        // dd($data);
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
            $this->translator->trans('registry.treatment.show.author'),
            $this->translator->trans('registry.treatment.show.coordonnees_responsable_traitement'),
            $this->translator->trans('registry.treatment.show.goal'),
            $this->translator->trans('registry.treatment.show.manager'),
            $this->translator->trans('registry.treatment.show.active'),
            $this->translator->trans('registry.treatment.show.legal_basis'),
            $this->translator->trans('registry.treatment.show.legal_basis_justification'),
            $this->translator->trans('registry.treatment.show.observation'),
            $this->translator->trans('registry.treatment.show.public_register'),
            $this->translator->trans('registry.treatment.show.exempt_AIPD'),
            $this->translator->trans('registry.treatment.show.dpo_message'),
        ];
    }

    private function initializeTreatmentGeneralInformations(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        $yes = $this->translator->trans('label.yes');
        $no  = $this->translator->trans('label.no');

        $goal                    = $treatment->getGoal();
        $legalBasisJustification = $treatment->getLegalBasisJustification();
        $observation             = $treatment->getObservation();
        if (!\is_null($goal) && isset($goal[0]) && '-' === $goal[0]) {
            $goal = ' ' . $goal;
        }
        if (!\is_null($legalBasisJustification) && isset($legalBasisJustification[0]) && '-' === $legalBasisJustification[0]) {
            $legalBasisJustification = ' ' . $legalBasisJustification;
        }
        if (!\is_null($observation) && isset($observation[0]) && '-' === $observation[0]) {
            $observation = ' ' . $observation;
        }

        return [
            !\is_null($treatment->getAuthor()) && array_key_exists($treatment->getAuthor(), TreatmentAuthorDictionary::getAuthors()) ? TreatmentAuthorDictionary::getAuthors()[$treatment->getAuthor()] : $treatment->getAuthor(),
            $treatment->getCoordonneesResponsableTraitement(),
            $goal,
            $treatment->getManager(),
            $treatment->isActive() ? $this->translator->trans('label.active') : $this->translator->trans('label.inactive'),
            !\is_null($treatment->getLegalBasis()) && array_key_exists($treatment->getLegalBasis(), TreatmentLegalBasisDictionary::getBasis()) ? TreatmentLegalBasisDictionary::getBasis()[$treatment->getLegalBasis()] : $treatment->getLegalBasis(),
            $legalBasisJustification,
            $observation,
            $treatment->isExemptAIPD() ? $yes : $no,
            $treatment->getPublic() ? $yes : $no,
            $treatment->getExemptAIPD() ? $yes : $no,
            $treatment->getDpoMessage(),
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
            implode(' - ', \iterable_to_array($treatment->getContractors())) ?: '',
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
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_usager'),
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_usager') . ' - Commentaire',
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_other'),
            $detailsTrans . ' - ' . $concernedPeople . ' - ' . $this->translator->trans('registry.treatment.show.concerned_people_other') . ' - Commentaire',
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.estimated_concerned_people'),
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.software'),
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.paper_processing'),
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.delay') . ' - Durée',
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.delay') . ' - Nom',
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.delay') . ' - Sort final',
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.data_origin'),
            $detailsTrans . ' - ' . $this->translator->trans('registry.treatment.show.collecting_method'),
        ];
    }

    private function initializeTreatmentDetails(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        $yes = $this->translator->trans('label.yes');
        $no  = $this->translator->trans('label.no');

        $shelfLifes = $treatment->getShelfLifes();

        $duration     = '';
        $name         = '';
        $ultimateFate = '';

        if (count($shelfLifes) > 0) {
            foreach ($shelfLifes as $key => $shelfLife) {
                $duration .= $key + 1 . ': ' . $shelfLife->duration . " \r\n";
                $name .= $key + 1 . ': ' . $shelfLife->name . "\r\n";
                $ultimateFate .= $key + 1 . ': ' . $shelfLife->ultimateFate . "\r\n";
            }
        }

        return [
            $treatment->getConcernedPeopleParticular()->isCheck() ? $yes : $no,
            $treatment->getConcernedPeopleParticular()->getComment(),
            $treatment->getConcernedPeopleUser()->isCheck() ? $yes : $no,
            $treatment->getConcernedPeopleUser()->getComment(),
            $treatment->getConcernedPeopleUsager()->isCheck() ? $yes : $no,
            $treatment->getConcernedPeopleUsager()->getComment(),
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
            $duration,
            $name,
            $ultimateFate,
            $treatment->getDataOrigin(),
            !\is_null($treatment->getCollectingMethod()) ? join(', ', array_map(function ($cm) {
                return array_key_exists($cm, TreatmentCollectingMethodDictionary::getMethods()) ? TreatmentCollectingMethodDictionary::getMethods()[$cm] : $cm;
            }, $treatment->getCollectingMethod())) : '',
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

    private function treatmentConformiteHeaders()
    {
        $conformiteTraitementHeaders = [
            $this->translator->trans('registry.treatment.show.conformite_traitement'),
            $this->translator->trans('registry.treatment.show.conformite_traitement_created_at'),
            $this->translator->trans('registry.treatment.show.conformite_traitement_updated_at'),
        ];

        foreach ($this->questionRepository->findAll(['position' => 'asc']) as $question) {
            $conformiteTraitementHeaders[] = $this->translator->trans('registry.treatment.show.conformite_traitement') . ' - Question : ' . $question->getQuestion();
        }

        return $conformiteTraitementHeaders;
    }

    private function initializeTreatmentConformite(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        if (!$treatment->getCollectivity()->isHasModuleConformiteTraitement()) {
            return ['Module non actif'];
        }

        $conformtiteTraitement = $treatment->getConformiteTraitement();
        if (\is_null($conformtiteTraitement)) {
            return ['Non effectuée'];
        }

        $data = [
            ConformiteTraitementLevelDictionary::getConformites()[ConformiteTraitementCompletion::getConformiteTraitementLevel($conformtiteTraitement)],
            $this->getDate($conformtiteTraitement->getCreatedAt()),
            $this->getDate($conformtiteTraitement->getUpdatedAt()),
        ];

        $responses = $treatment->getConformiteTraitement()->getReponses();

        $ordered = [];
        foreach ($responses as $reponse) {
            $ordered[$reponse->getQuestion()->getPosition()] = $reponse;
        }

        \ksort($ordered);

        foreach ($ordered as $reponse) {
            if ($reponse->isConforme()) {
                $data[] = 'Conforme';
                continue;
            }

            if (\count($reponse->getActionProtections()) > 0) {
                $planified = array_filter(\iterable_to_array($reponse->getActionProtections()), function (Mesurement $mesurement) {
                    return MesurementStatusDictionary::STATUS_NOT_APPLIED === $mesurement->getStatus()
                        && !\is_null($mesurement->getPlanificationDate())
                    ;
                });

                if (\count($planified) > 0) {
                    $data[] = 'Non-conforme mineure';
                    continue;
                }
            }

            $data[] = 'Non-conforme majeure';
        }

        return $data;
    }
}
