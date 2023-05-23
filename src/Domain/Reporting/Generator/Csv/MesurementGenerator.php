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

use App\Domain\Registry\Dictionary\MesurementPriorityDictionary;
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\User\Repository\Collectivity;
use App\Infrastructure\ORM\Registry\Repository\Mesurement;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class MesurementGenerator extends AbstractGenerator
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
        Mesurement $mesurementRepository,
        Security $security
    ) {
        $this->translator             = $translatorInterface;
        $this->collectivityRepository = $collectivityRepository;
        $this->mesurementRepository   = $mesurementRepository;
        $this->security               = $security;
    }

    public function initializeExtract(): array
    {
        $this->yes = $this->translator->trans('label.yes');
        $this->no  = $this->translator->trans('label.no');

        $headers = array_merge(
            ['Nom'],
            $this->collectivityHeaders(),
            $this->mesurementGeneralInformationsHeaders(),
            $this->mesurementApplicationHeaders(),
            $this->mesurementProofHeaders(),
            $this->mesurementHistoricHeaders(),
        );
        $data = [$headers];

        $user = null;
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser();
        }

        /** @var \App\Domain\Registry\Model\Mesurement $mesurement */
        foreach ($this->mesurementRepository->findAllByActiveCollectivity(true, $user) as $mesurement) {
            $extract = array_merge(
                [$mesurement->getName()],
                $this->initializeCollectivity($mesurement->getCollectivity()),
                $this->initializeMesurementGeneralInformations($mesurement),
                $this->initializeMesurementApplication($mesurement),
                $this->initializeProof($mesurement),
                $this->initializeMesurementHistoric($mesurement),
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

    private function mesurementGeneralInformationsHeaders()
    {
        return [
            $this->translator->trans('registry.mesurement.show.description'),
            $this->translator->trans('registry.mesurement.show.manager'),
            $this->translator->trans('registry.mesurement.show.priority'),
            $this->translator->trans('registry.mesurement.show.cost'),
            $this->translator->trans('registry.mesurement.show.charge'),
        ];
    }

    private function initializeMesurementGeneralInformations(\App\Domain\Registry\Model\Mesurement $mesurement): array
    {
        return [
            $mesurement->getDescription(),
            $mesurement->getManager(),
            !\is_null($mesurement->getPriority()) ? MesurementPriorityDictionary::getPriorities()[$mesurement->getPriority()] : null,
            $mesurement->getCost(),
            $mesurement->getCharge(),
        ];
    }

    private function mesurementApplicationHeaders()
    {
        $applicationTrans = $this->translator->trans('registry.mesurement.tab.application');

        return [
            $applicationTrans . ' - ' . $this->translator->trans('registry.mesurement.show.status'),
            $applicationTrans . ' - ' . $this->translator->trans('registry.mesurement.show.planification_date'),
            $applicationTrans . ' - ' . $this->translator->trans('registry.mesurement.show.comment'),
        ];
    }

    private function initializeMesurementApplication(\App\Domain\Registry\Model\Mesurement $mesurement): array
    {
        return [
            !\is_null($mesurement->getStatus()) ? MesurementStatusDictionary::getStatus()[$mesurement->getStatus()] : null,
            $this->getDate($mesurement->getPlanificationDate(), GeneratorInterface::DATE_FORMAT),
            $mesurement->getComment(),
        ];
    }

    private function mesurementProofHeaders()
    {
        return [
            $this->translator->trans('label.linked_documents'),
        ];
    }

    private function initializeProof(\App\Domain\Registry\Model\Mesurement $mesurement): array
    {
        return [
            implode(' - ', \iterable_to_array($mesurement->getProofs())),
        ];
    }

    private function mesurementHistoricHeaders()
    {
        $historicTrans = $this->translator->trans('registry.mesurement.tab.historic');

        return [
            $historicTrans . ' - ' . $this->translator->trans('registry.mesurement.show.creator'),
            $historicTrans . ' - ' . $this->translator->trans('registry.mesurement.show.created_at'),
            $historicTrans . ' - ' . $this->translator->trans('registry.mesurement.show.updated_at'),
        ];
    }

    private function initializeMesurementHistoric(\App\Domain\Registry\Model\Mesurement $mesurement): array
    {
        return [
            strval($mesurement->getCreator()),
            $this->getDate($mesurement->getCreatedAt()),
            $this->getDate($mesurement->getUpdatedAt()),
        ];
    }
}
