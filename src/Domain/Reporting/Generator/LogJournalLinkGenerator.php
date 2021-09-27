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

namespace App\Domain\Reporting\Generator;

use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Model\LogJournal;
use App\Domain\User\Model\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class LogJournalLinkGenerator
{
    const DELETE_LABEL = 'Supprimé';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager)
    {
        $this->router           = $router;
        $this->entityManager    = $entityManager;
    }

    public function getLink(LogJournal $log)
    {
        if ($log->isDeleted()) {
            return self::DELETE_LABEL;
        }

        $id = $log->getSubjectId();

        switch ($log->getSubjectType()) {
            case LogJournalSubjectDictionary::USER_USER:
            case LogJournalSubjectDictionary::USER_EMAIL:
            case LogJournalSubjectDictionary::USER_PASSWORD:
            case LogJournalSubjectDictionary::USER_FIRSTNAME:
            case LogJournalSubjectDictionary::USER_LASTNAME:
                return $this->router->generate('user_user_edit', ['id' => $id]);
            case LogJournalSubjectDictionary::REGISTRY_CONFORMITE_TRAITEMENT:
            case LogJournalSubjectDictionary::REGISTRY_PROOF:
            case LogJournalSubjectDictionary::MATURITY_SURVEY:
                return $this->router->generate($log->getSubjectType() . '_edit', ['id' => $id]);
            case LogJournalSubjectDictionary::REGISTRY_CONFORMITE_ORGANISATION_EVALUATION:
                return $this->router->generate('registry_conformite_organisation_edit', ['id' => $id]);
            case LogJournalSubjectDictionary::USER_SERVICE:
                $service = $this
                ->entityManager
                ->getRepository(Service::class)
                ->findOneBy(['id' => $id]);

                return $this->router->generate('user_collectivity_show', ['id' => $service->getCollectivity()->getId()]);
            default:
                return $this->router->generate($log->getSubjectType() . '_show', ['id' => $id]);
        }
    }
}
