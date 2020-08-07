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

use App\Domain\Registry\Dictionary\ProofTypeDictionary;
use App\Domain\Registry\Repository\ConformiteOrganisation\Processus;
use App\Domain\Registry\Repository\Contractor;
use App\Domain\Registry\Repository\Mesurement;
use App\Domain\Registry\Repository\Proof;
use App\Domain\Registry\Repository\Request;
use App\Domain\Registry\Repository\Violation;
use App\Domain\Registry\Service\ConformiteOrganisationService;
use App\Domain\User\Dictionary\CollectivityTypeDictionary;
use App\Domain\User\Dictionary\ContactCivilityDictionary;
use App\Domain\User\Repository\Collectivity;
use App\Infrastructure\ORM\Maturity\Repository\Survey;
use App\Infrastructure\ORM\Registry\Repository\ConformiteOrganisation\Evaluation;
use App\Infrastructure\ORM\Registry\Repository\Treatment;
use App\Infrastructure\ORM\User\Repository\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class CollectivityGenerator extends AbstractGenerator
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
     * @var Mesurement
     */
    private $mesurementRepository;

    /**
     * @var Survey
     */
    private $surveyRepository;

    /**
     * @var User
     */
    private $userRepository;

    /**
     * @var Proof
     */
    private $proofRepository;

    /**
     * @var Treatment
     */
    private $treatmentRepository;

    /**
     * @var Contractor
     */
    private $contractorRepository;

    /**
     * @var Violation
     */
    private $violationRepository;

    /**
     * @var Request
     */
    private $requestRepository;

    /**
     * @var Processus
     */
    private $processsusRepository;

    /**
     * @var Evaluation
     */
    private $evaluationRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var string
     */
    private $defaultDpoCivility;
    /**
     * @var string
     */
    private $defaultDpoFirstName;
    /**
     * @var string
     */
    private $defaultDpoLastName;
    /**
     * @var string
     */
    private $defaultDpoCompany;
    /**
     * @var string
     */
    private $defaultDpoJob;
    /**
     * @var string
     */
    private $defaultDpoMail;
    /**
     * @var string
     */
    private $defaultDpoPhoneNumber;

    public function __construct(
        TranslatorInterface $translatorInterface,
        Collectivity $collectivityRepository,
        Mesurement $mesurementRepository,
        Survey $surveyRepository,
        User $userRepository,
        Proof $proofRepository,
        Treatment $treatmentRepository,
        Contractor $contractorRepository,
        Violation $violationRepository,
        Request $requestRepository,
        Processus $processusRepository,
        Evaluation $evaluationRepository,
        Security $security,
        string $defaultDpoCivility,
        string $defaultDpoFirstName,
        string $defaultDpoLastName,
        string $defaultDpoCompany,
        string $defaultDpoJob,
        string $defaultDpoMail,
        string $defaultDpoPhoneNumber
    ) {
        $this->translator             = $translatorInterface;
        $this->collectivityRepository = $collectivityRepository;
        $this->mesurementRepository   = $mesurementRepository;
        $this->surveyRepository       = $surveyRepository;
        $this->userRepository         = $userRepository;
        $this->proofRepository        = $proofRepository;
        $this->treatmentRepository    = $treatmentRepository;
        $this->contractorRepository   = $contractorRepository;
        $this->violationRepository    = $violationRepository;
        $this->requestRepository      = $requestRepository;
        $this->processsusRepository   = $processusRepository;
        $this->evaluationRepository   = $evaluationRepository;
        $this->security               = $security;
        $this->defaultDpoCivility     = $defaultDpoCivility;
        $this->defaultDpoFirstName    = $defaultDpoFirstName;
        $this->defaultDpoLastName     = $defaultDpoLastName;
        $this->defaultDpoCompany      = $defaultDpoCompany;
        $this->defaultDpoJob          = $defaultDpoJob;
        $this->defaultDpoMail         = $defaultDpoMail;
        $this->defaultDpoPhoneNumber  = $defaultDpoPhoneNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function initializeExtract(): array
    {
        $headers = array_merge(
            $this->collectivityHeaders(),
            $this->registryHeaders(),
            $this->surveyHeaders(),
            $this->userHeaders(),
            $this->proofHeaders(),
            $this->conformiteOrganisationHeaders(),
        );
        $data = [$headers];
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $collectivities = $this->collectivityRepository->findAll();
        } else {
            $collectivities = $this->collectivityRepository->findByUserReferent($this->security->getUser(), true);
        }

        foreach ($collectivities as $collectivity) {
            $extract = array_merge(
                $this->initializeCollectivity($collectivity),
                $this->initializeRegistry($collectivity),
                $this->initializeSurvey($collectivity),
                $this->initializeUser($collectivity),
                $this->initializeProof($collectivity),
                $this->initializeConformiteOrganisation($collectivity)
            );
            array_push($data, $extract);
        }

        return $data;
    }

    private function collectivityHeaders(): array
    {
        $legalManager = $this->translator->trans('user.collectivity.tab.legal_manager');
        $itManager    = $this->translator->trans('user.collectivity.tab.it_manager');
        $referent     = $this->translator->trans('user.collectivity.tab.referent');
        $dpo          = $this->translator->trans('user.collectivity.tab.dpo');

        return [
            'Id',
            $this->translator->trans('user.collectivity.show.name'),
            $this->translator->trans('user.collectivity.show.short_name'),
            $this->translator->trans('user.collectivity.show.type'),
            $this->translator->trans('user.collectivity.show.siren'),
            $this->translator->trans('user.collectivity.show.active'),
            $this->translator->trans('user.collectivity.show.website'),
            $this->translator->trans('user.collectivity.show.has_module_conformite_traitement'),
            $this->translator->trans('user.collectivity.show.has_module_conformite_organisation'),
            $this->translator->trans('user.collectivity.show.address_line_one'),
            $this->translator->trans('user.collectivity.show.address_line_two'),
            $this->translator->trans('user.collectivity.show.address_zip_code'),
            $this->translator->trans('user.collectivity.show.address_city'),
            $this->translator->trans('user.collectivity.show.address_insee'),
            $legalManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_civility'),
            $legalManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_first_name'),
            $legalManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_last_name'),
            $legalManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_job'),
            $legalManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_mail'),
            $legalManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_phone_number'),
            $itManager . ' - ' . $this->translator->trans('user.collectivity.show.different_it_manager'),
            $itManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_civility'),
            $itManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_first_name'),
            $itManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_last_name'),
            $itManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_job'),
            $itManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_mail'),
            $itManager . ' - ' . $this->translator->trans('user.collectivity.show.contact_phone_number'),
            $referent . ' - ' . $this->translator->trans('user.collectivity.show.contact_civility'),
            $referent . ' - ' . $this->translator->trans('user.collectivity.show.contact_first_name'),
            $referent . ' - ' . $this->translator->trans('user.collectivity.show.contact_last_name'),
            $referent . ' - ' . $this->translator->trans('user.collectivity.show.contact_job'),
            $referent . ' - ' . $this->translator->trans('user.collectivity.show.contact_mail'),
            $referent . ' - ' . $this->translator->trans('user.collectivity.show.contact_phone_number'),
            $dpo . ' - ' . $this->translator->trans('user.collectivity.show.different_dpo'),
            $dpo . ' - ' . $this->translator->trans('user.collectivity.show.contact_civility'),
            $dpo . ' - ' . $this->translator->trans('user.collectivity.show.contact_first_name'),
            $dpo . ' - ' . $this->translator->trans('user.collectivity.show.contact_last_name'),
            $dpo . ' - ' . $this->translator->trans('user.collectivity.show.contact_job'),
            $dpo . ' - ' . $this->translator->trans('user.collectivity.show.contact_mail'),
            $dpo . ' - ' . $this->translator->trans('user.collectivity.show.contact_phone_number'),
            $this->translator->trans('user.collectivity.tab.reporting_block.management_commitment'),
            $this->translator->trans('user.collectivity.tab.reporting_block.continuous_improvement'),
            $this->translator->trans('user.collectivity.show.comite_il_short'),
            $this->translator->trans('user.collectivity.show.created_at'),
            $this->translator->trans('user.collectivity.show.updated_at'),
        ];
    }

    private function initializeCollectivity(\App\Domain\User\Model\Collectivity $collectivity)
    {
        $yes = $this->translator->trans('label.yes');
        $no  = $this->translator->trans('label.no');

        $legalManager = $collectivity->getLegalManager();
        $itManager    = $collectivity->getItManager();
        $referent     = $collectivity->getReferent();
        $dpo          = $collectivity->getDpo();

        return [
            $collectivity->getId()->toString(),
            $collectivity->getName(),
            $collectivity->getShortName(),
            !\is_null($collectivity->getType()) ? CollectivityTypeDictionary::getTypes()[$collectivity->getType()] : '',
            $collectivity->getSiren(),
            $collectivity->isActive() ? $this->translator->trans('label.active') : $this->translator->trans('label.inactive'),
            $collectivity->getWebsite(),
            $collectivity->isHasModuleConformiteTraitement() ? $yes : $no,
            $collectivity->isHasModuleConformiteOrganisation() ? $yes : $no,
            $collectivity->getAddress()->getLineOne(),
            $collectivity->getAddress()->getLineTwo(),
            $collectivity->getAddress()->getZipCode(),
            $collectivity->getAddress()->getCity(),
            $collectivity->getAddress()->getInsee(),
            !\is_null($legalManager->getCivility()) ? ContactCivilityDictionary::getCivilities()[$legalManager->getCivility()] : '',
            $legalManager->getFirstName(),
            $legalManager->getLastName(),
            $legalManager->getJob(),
            $legalManager->getMail(),
            $legalManager->getPhoneNumber(),
            $collectivity->isDifferentItManager() ? $yes : $no,
            !\is_null($itManager->getCivility()) ? ContactCivilityDictionary::getCivilities()[$itManager->getCivility()] : '',
            $itManager->getFirstName(),
            $itManager->getLastName(),
            $itManager->getJob(),
            $itManager->getMail(),
            $itManager->getPhoneNumber(),
            !\is_null($referent->getCivility()) ? ContactCivilityDictionary::getCivilities()[$referent->getCivility()] : '',
            $referent->getFirstName(),
            $referent->getLastName(),
            $referent->getJob(),
            $referent->getMail(),
            $referent->getPhoneNumber(),
            $collectivity->isDifferentDpo() ? $yes : $no,
            !\is_null($dpo->getCivility()) ? ContactCivilityDictionary::getCivilities()[$dpo->getCivility()] : ContactCivilityDictionary::getCivilities()[$this->defaultDpoCivility],
            !\is_null($dpo->getFirstName()) ? $dpo->getFirstName() : $this->defaultDpoFirstName,
            !\is_null($dpo->getLastName()) ? $dpo->getLastName() : $this->defaultDpoLastName,
            !\is_null($dpo->getJob()) ? $dpo->getJob() : $this->defaultDpoJob,
            !\is_null($dpo->getMail()) ? $dpo->getMail() : $this->defaultDpoMail,
            !\is_null($dpo->getPhoneNumber()) ? $dpo->getPhoneNumber() : $this->defaultDpoPhoneNumber,
            !\is_null($collectivity->getReportingBlockManagementCommitment()) ? \strip_tags($collectivity->getReportingBlockManagementCommitment()) : '',
            !\is_null($collectivity->getReportingBlockContinuousImprovement()) ? \strip_tags(html_entity_decode($collectivity->getReportingBlockContinuousImprovement(), ENT_QUOTES | ENT_HTML401)) : '',
            !$collectivity->getComiteIlContacts()->isEmpty() ? json_encode($collectivity->getComiteIlContacts()->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '',
            $this->getDate($collectivity->getCreatedAt()),
            $this->getDate($collectivity->getUpdatedAt()),
        ];
    }

    private function registryHeaders()
    {
        return [
            'Actions de protection réalisées',
            'Actions de protection planifiées',
            'Nombre de preuves déposées',
            'Nombre de traitements',
            'Nombre de sous traitants',
            'Nombre de violation de données',
            'Nombre de demandes de personnes concernées',
            'Date du dernier traitement modifié',
            'Date de la dernière violation',
            'Date de la dernière demande',
        ];
    }

    private function initializeRegistry(\App\Domain\User\Model\Collectivity $collectivity): array
    {
        $lastUpdatedTreatment = $this->treatmentRepository->findOneOrNullLastUpdateByCollectivity($collectivity);
        $lastUpdatedViolation = $this->violationRepository->findOneOrNullLastUpdateByCollectivity($collectivity);
        $lastUpdatedRequest   = $this->requestRepository->findOneOrNullLastUpdateByCollectivity($collectivity);

        return [
            $this->mesurementRepository->countAppliedByCollectivity($collectivity),
            $this->mesurementRepository->countPlanifiedByCollectivity($collectivity),
            $this->proofRepository->countAllByCollectivity($collectivity),
            $this->treatmentRepository->countAllByCollectivity($collectivity),
            $this->contractorRepository->countAllByCollectivity($collectivity),
            $this->violationRepository->countAllByCollectivity($collectivity),
            $this->requestRepository->countAllByCollectivity($collectivity),
            !\is_null($lastUpdatedTreatment) ? $this->getDate($lastUpdatedTreatment->getUpdatedAt()) : null,
            !\is_null($lastUpdatedViolation) ? $this->getDate($lastUpdatedViolation->getUpdatedAt()) : null,
            !\is_null($lastUpdatedRequest) ? $this->getDate($lastUpdatedRequest->getUpdatedAt()) : null,
        ];
    }

    private function surveyHeaders()
    {
        return [
            'Dernier indice de maturité - date',
            'Dernier indice de maturité - score',
        ];
    }

    private function initializeSurvey(\App\Domain\User\Model\Collectivity $collectivity): array
    {
        $surveys = $this->surveyRepository->findAllByCollectivity($collectivity, ['createdAt' => 'desc'], 1);

        /** @var \App\Domain\Maturity\Model\Survey|null $survey */
        $survey = isset($surveys[0]) ? $surveys[0] : null;

        return [
            !\is_null($survey) ? $this->getDate($survey->getCreatedAt()) : null,
            !\is_null($survey) ? $survey->getScore() : null,
        ];
    }

    private function userHeaders()
    {
        return [
            'Dernier utilisateur connecté - Date dernière connextion',
            'Dernier utilisateur connecté - Nom',
            'Dernier utilisateur connecté - Email',
        ];
    }

    private function initializeUser(\App\Domain\User\Model\Collectivity $collectivity): array
    {
        $user = $this->userRepository->findOneOrNullLastLoginUserByCollectivity($collectivity);

        return [
            !\is_null($user) ? $this->getDate($user->getLastLogin()) : null,
            !\is_null($user) ? $user->getFullName() : null,
            !\is_null($user) ? $user->getEmail() : null,
        ];
    }

    private function proofHeaders()
    {
        $headers = [];

        foreach (ProofTypeDictionary::getTypes() as $type) {
            $headers[] = 'Dernier dépot de preuve - ' . $type;
        }

        return $headers;
    }

    private function initializeProof(\App\Domain\User\Model\Collectivity $collectivity)
    {
        $data = [];

        foreach (ProofTypeDictionary::getTypesKeys() as $type) {
            $proof = $this->proofRepository->findOneOrNullByTypeAndCollectivity($type, $collectivity);

            $data[] = !\is_null($proof) ? $this->getDate($proof->getCreatedAt()) : null;
        }

        return $data;
    }

    private function conformiteOrganisationHeaders()
    {
        $headers = ['Brouillon'];

        foreach ($this->processsusRepository->findAll(['position' => 'asc']) as $processus) {
            $headers[] = 'Conformité processus - ' . $processus->getNom();
            $headers[] = 'Conformité processus - ' . $processus->getNom() . ' - Pilote';
        }

        return $headers;
    }

    private function initializeConformiteOrganisation(\App\Domain\User\Model\Collectivity $collectivity)
    {
        $data = [];

        $conformiteOrganisationEvaluation = $this->evaluationRepository->findLastByOrganisation($collectivity);

        if ($collectivity->isHasModuleConformiteOrganisation() && null !== $conformiteOrganisationEvaluation) {
            $conformites = ConformiteOrganisationService::getOrderedConformites($conformiteOrganisationEvaluation);

            $data[] = $conformiteOrganisationEvaluation->isDraft() ? 'Oui' : 'Non';
            foreach ($conformites as $conformite) {
                $data[] = $conformite->getConformite();
                $data[] = $conformite->getPilote();
            }
        }

        return $data;
    }
}
