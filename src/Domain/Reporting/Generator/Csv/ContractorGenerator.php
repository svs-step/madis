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

use App\Domain\User\Dictionary\ContactCivilityDictionary;
use App\Domain\User\Repository\Collectivity;
use App\Infrastructure\ORM\Registry\Repository\Contractor;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContractorGenerator extends AbstractGenerator
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
     * @var Contractor
     */
    private $contractorRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var string
     */
    private $yes;

    /**
     * @var string
     */
    private $no;

    public function __construct(
        TranslatorInterface $translatorInterface,
        Collectivity $collectivityRepository,
        Contractor $contractorRepository,
        Security $security
    ) {
        $this->translator             = $translatorInterface;
        $this->collectivityRepository = $collectivityRepository;
        $this->contractorRepository   = $contractorRepository;
        $this->security               = $security;
    }

    public function initializeExtract(): array
    {
        $this->yes = $this->translator->trans('global.label.yes');
        $this->no  = $this->translator->trans('global.label.no');

        $headers = array_merge(
            ['Nom'],
            $this->collectivityHeaders(),
            $this->contractorGeneralInformationsHeaders(),
            $this->contractorDpoHeaders(),
            $this->contractorHistoricHeaders(),
            $this->contractorCoordinatesHeaders(),
            $this->contractorProofHeaders(),
            $this->contractorTreatmentHeaders(),
        );
        $data = [$headers];

        $user = null;
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser();
        }

        /** @var \App\Domain\Registry\Model\Contractor $contractor */
        foreach ($this->contractorRepository->findAllByActiveCollectivity(true, $user) as $contractor) {
            $extract = array_merge(
                [$contractor->getName()],
                $this->initializeCollectivity($contractor->getCollectivity()),
                $this->initializeContractorGeneralInformations($contractor),
                $this->initializeContractorDpo($contractor),
                $this->initializeContractorHistoric($contractor),
                $this->initializeContractorCoordinates($contractor),
                $this->initializeProof($contractor),
                $this->initializeTreatment($contractor),
            );
            array_push($data, $extract);
        }

        return $data;
    }

    private function collectivityHeaders(): array
    {
        $collectivityTrans = $this->translator->trans('global.label.organization');

        return [
            $collectivityTrans . ' - ' . $this->translator->trans('user.organization.label.name'),
            $collectivityTrans . ' - ' . $this->translator->trans('user.organization.label.siren'),
            $collectivityTrans . ' - ' . $this->translator->trans('global.label.address.insee'),
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

    private function contractorGeneralInformationsHeaders()
    {
        return [
            $this->translator->trans('registry.contractor.label.referent'),
            $this->translator->trans('registry.contractor.label.contractual_clauses_verified'),
            $this->translator->trans('registry.contractor.label.adopted_security_features'),
            $this->translator->trans('registry.contractor.label.maintains_treatment_register'),
            $this->translator->trans('registry.contractor.label.sending_data_outside_eu'),
            $this->translator->trans('registry.contractor.label.other_informations'),
        ];
    }

    private function initializeContractorGeneralInformations(\App\Domain\Registry\Model\Contractor $contractor): array
    {
        return [
            $contractor->getReferent(),
            $contractor->isContractualClausesVerified() ? $this->yes : $this->no,
            $contractor->isAdoptedSecurityFeatures() ? $this->yes : $this->no,
            $contractor->isMaintainsTreatmentRegister() ? $this->yes : $this->no,
            $contractor->isSendingDataOutsideEu() ? $this->yes : $this->no,
            $contractor->getOtherInformations(),
        ];
    }

    private function contractorDpoHeaders()
    {
        $dpo = $this->translator->trans('registry.contractor.tab.dpo');

        return [
            $dpo . ' - ' . $this->translator->trans('registry.contractor.label.has_dpo'),
            $dpo . ' - ' . $this->translator->trans('global.label.contact.civility'),
            $dpo . ' - ' . $this->translator->trans('global.label.contact.first_name'),
            $dpo . ' - ' . $this->translator->trans('global.label.contact.last_name'),
            $dpo . ' - ' . $this->translator->trans('global.label.contact.job'),
            $dpo . ' - ' . $this->translator->trans('global.label.contact.email'),
            $dpo . ' - ' . $this->translator->trans('global.label.contact.phone_number'),
        ];
    }

    private function initializeContractorDpo(\App\Domain\Registry\Model\Contractor $contractor): array
    {
        $dpo = $contractor->getDpo();

        return [
            $contractor->isHasDpo() ? $this->yes : $this->no,
            $contractor->isHasDpo() && !\is_null($dpo) && !\is_null($dpo->getCivility()) ? ContactCivilityDictionary::getCivilities()[$dpo->getCivility()] : null,
            $contractor->isHasDpo() && !\is_null($dpo) ? $dpo->getFirstName() : null,
            $contractor->isHasDpo() && !\is_null($dpo) ? $dpo->getLastName() : null,
            $contractor->isHasDpo() && !\is_null($dpo) ? $dpo->getJob() : null,
            $contractor->isHasDpo() && !\is_null($dpo) ? $dpo->getMail() : null,
            $contractor->isHasDpo() && !\is_null($dpo) ? $dpo->getPhoneNumber() : null,
        ];
    }

    private function treatmentRecipientsHeaders()
    {
        $recipientsTrans = $this->translator->trans('registry.treatment.tab.recipients');

        return [
            $recipientsTrans . ' - ' . $this->translator->trans('registry.treatment.label.recipient_category'),
            $recipientsTrans . ' - ' . $this->translator->trans('global.label.linked_contractor'),
        ];
    }

    private function initializeTreatmentRecipients(\App\Domain\Registry\Model\Treatment $treatment): array
    {
        return [
            $treatment->getRecipientCategory(),
            implode(' - ', \iterable_to_array($treatment->getContractors())),
        ];
    }

    private function contractorHistoricHeaders()
    {
        $historicTrans = $this->translator->trans('global.tab.history');

        return [
            $historicTrans . ' - ' . $this->translator->trans('global.label.created_by'),
            $historicTrans . ' - ' . $this->translator->trans('global.label.created_at'),
            $historicTrans . ' - ' . $this->translator->trans('global.label.updated_at'),
        ];
    }

    private function initializeContractorHistoric(\App\Domain\Registry\Model\Contractor $contractor): array
    {
        return [
            strval($contractor->getCreator()),
            $this->getDate($contractor->getCreatedAt()),
            $this->getDate($contractor->getUpdatedAt()),
        ];
    }

    private function contractorCoordinatesHeaders()
    {
        $coordinates = $this->translator->trans('registry.contractor.tab.coordinates');

        return [
            $coordinates . ' - ' . $this->translator->trans('global.label.contact.first_name'),
            $coordinates . ' - ' . $this->translator->trans('global.label.contact.last_name'),
            $coordinates . ' - ' . $this->translator->trans('global.label.address.line_one'),
            $coordinates . ' - ' . $this->translator->trans('global.label.address.line_two'),
            $coordinates . ' - ' . $this->translator->trans('global.label.address.zip_code'),
            $coordinates . ' - ' . $this->translator->trans('global.label.address.city'),
            $coordinates . ' - ' . $this->translator->trans('global.label.contact.email'),
            $coordinates . ' - ' . $this->translator->trans('global.label.contact.phone_number'),
        ];
    }

    private function initializeContractorCoordinates(\App\Domain\Registry\Model\Contractor $contractor): array
    {
        $legalManager = $contractor->getLegalManager();
        $address      = $contractor->getAddress();

        return [
            !\is_null($legalManager) ? $legalManager->getFirstName() : null,
            !\is_null($legalManager) ? $legalManager->getLastName() : null,
            !\is_null($address) ? $address->getLineOne() : null,
            !\is_null($address) ? $address->getLineTwo() : null,
            !\is_null($address) ? $address->getZipCode() : null,
            !\is_null($address) ? $address->getCity() : null,
            !\is_null($address) ? $address->getMail() : null,
            !\is_null($address) ? $address->getPhoneNumber() : null,
        ];
    }

    private function contractorProofHeaders()
    {
        return [
            $this->translator->trans('global.label.linked_proof'),
        ];
    }

    private function initializeProof(\App\Domain\Registry\Model\Contractor $contractor): array
    {
        return [
            implode(' - ', \iterable_to_array($contractor->getProofs())),
        ];
    }

    private function contractorTreatmentHeaders()
    {
        return [
            $this->translator->trans('global.label.linked_treatment'),
        ];
    }

    private function initializeTreatment(\App\Domain\Registry\Model\Contractor $contractor): array
    {
        return [
            implode(' - ', \iterable_to_array($contractor->getTreatments())),
        ];
    }
}
