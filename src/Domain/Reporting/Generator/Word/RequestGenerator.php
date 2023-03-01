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

namespace App\Domain\Reporting\Generator\Word;

use App\Domain\Registry\Dictionary\RequestAnswerTypeDictionary;
use App\Domain\Registry\Dictionary\RequestCivilityDictionary;
use App\Domain\Registry\Dictionary\RequestObjectDictionary;
use App\Domain\Registry\Dictionary\RequestStateDictionary;
use App\Domain\Registry\Model\Request;
use PhpOffice\PhpWord\Element\Section;

class RequestGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    /**
     * Global overview : Information to display for request in overview report.
     *
     * @throws \Exception
     */
    public function addGlobalOverview(Section $section, array $data): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        // Aggregate data before rendering
        $tableData = [
            [
                'Personne concernée',
                'Date de la demande',
                'Objet',
                'Date de traitement',
                'État de la demande',
            ],
        ];
        $nbTotal = \count($data);
        foreach ($data as $request) {
            if ($request->getApplicant()->isConcernedPeople() || ' ' === $request->getConcernedPeople()->getFullName()) {
                $concernedPeople = $request->getApplicant()->getFullName();
            } else {
                $concernedPeople = $request->getConcernedPeople()->getFullName();
            }

            $tableData[] = [
                $concernedPeople,
                $request->getDate() ? $this->getDate($request->getDate(), 'd/m/Y') : '',
                array_key_exists($request->getObject(), RequestObjectDictionary::getObjects()) ? RequestObjectDictionary::getObjects()[$request->getObject()] : $request->getObject(),
                $request->getAnswer() ? $this->getDate($request->getAnswer()->getDate(), 'd/m/Y') : '',
                array_key_exists($request->getState(), RequestStateDictionary::getStates()) ? RequestStateDictionary::getStates()[$request->getState()] : $request->getState(),
            ];
        }

        $section->addTitle('Registre des demandes de personnes concernées', 2);

        if (empty($data)) {
            $section->addText('Il n’y a aucune demande des personnes concernées.');

            return;
        }

        $section->addText("Un registre des demandes des personnes concernées est tenu à jour par '{$collectivity}'.");
        $section->addText("Il y a eu {$nbTotal} demandes des personnes concernées.");

        if (0 < $nbTotal) {
            $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addSyntheticView(Section $section, array $data): void
    {
        $section->addTitle('Liste des demandes', 1);

        // Aggregate data before rendering
        $tableData = [
            [
                'Personne concernée',
                'Date de la demande',
                'Objet',
                'Date de traitement',
            ],
        ];
        foreach ($data as $request) {
            if ($request->getApplicant()->isConcernedPeople() || ' ' === $request->getConcernedPeople()->getFullName()) {
                $concernedPeople = $request->getApplicant()->getFullName();
            } else {
                $concernedPeople = $request->getConcernedPeople()->getFullName();
            }

            $tableData[] = [
                $concernedPeople,
                $this->getDate($request->getDate(), 'd/m/Y'),
                RequestObjectDictionary::getObjects()[$request->getObject()],
                $this->getDate($request->getAnswer()->getDate(), 'd/m/Y'),
            ];
        }

        // Rendering
        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
        $section->addPageBreak();
    }

    /**
     * {@inheritdoc}
     */
    public function addDetailedView(Section $section, array $data): void
    {
        $section->addTitle('Détail des demandes', 1);

        /** @var Request $request */
        foreach ($data as $key => $request) {
            if (0 !== $key) {
                $section->addPageBreak();
            }

            $requestData = [
                [
                    'Objet de la demande',
                    $request->getObject() ? RequestObjectDictionary::getObjects()[$request->getObject()] : null,
                ],
                [
                    'Demande explicite',
                    $request->getOtherObject(),
                ],
                [
                    'Date de la demande',
                    $this->getDate($request->getDate(), 'd/m/Y'),
                ],
                [
                    'Motif',
                    $request->getReason(),
                ],
                [
                    'Demande complète',
                    $request->isComplete() ? 'Oui' : 'Non',
                ],
                [
                    'Demandeur légitime',
                    $request->isLegitimateApplicant() ? 'Oui' : 'Non',
                ],
                [
                    'Demande légitime',
                    $request->isLegitimateRequest() ? 'Oui' : 'Non',
                ],
            ];

            $applicant     = $request->getApplicant();
            $applicantData = [
                [
                    'Civilité',
                    $applicant->getCivility() ? RequestCivilityDictionary::getCivilities()[$applicant->getCivility()] : null,
                ],
                [
                    'Prénom',
                    $applicant->getFirstName(),
                ],
                [
                    'Nom',
                    $applicant->getLastName(),
                ],
                [
                    'Adresse',
                    $applicant->getAddress(),
                ],
                [
                    'Email',
                    $applicant->getMail(),
                ],
                [
                    'N° de téléphone',
                    $applicant->getPhoneNumber(),
                ],
                [
                    'Est la personne concernée',
                    $applicant->isConcernedPeople() ? 'Oui' : 'Non',
                ],
            ];

            $concernedPeople     = $request->getConcernedPeople();
            $concernedPeopleData = [
                [
                    'Civilité',
                    $concernedPeople->getCivility() ? RequestCivilityDictionary::getCivilities()[$concernedPeople->getCivility()] : null,
                ],
                [
                    'Prénom',
                    $concernedPeople->getFirstName(),
                ],
                [
                    'Nom',
                    $concernedPeople->getLastName(),
                ],
                [
                    'Adresse',
                    $concernedPeople->getAddress(),
                ],
                [
                    'Email',
                    $concernedPeople->getMail(),
                ],
                [
                    'N° de téléphone',
                    $concernedPeople->getPhoneNumber(),
                ],
                [
                    'Lien avec le demandeur',
                    $concernedPeople->getLinkWithApplicant(),
                ],
            ];

            $response     = $request->getAnswer();
            $responseData = [
                [
                    'État de la demande',
                    RequestStateDictionary::getStates()[$request->getState()],
                ],
                [
                    'Réponse apportée',
                    $response->getResponse(),
                ],
                [
                    'Date de la réponse',
                    $this->getDate($response->getDate(), 'd/m/Y'),
                ],
                [
                    'Moyen de la réponse',
                    $response->getType() ? RequestAnswerTypeDictionary::getTypes()[$response->getType()] : null,
                ],
            ];

            if (RequestStateDictionary::STATE_DENIED === $request->getState()) {
                $responseData[] = [
                    'Motif du refus',
                    $request->getStateRejectionReason(),
                ];
            }

            $historyData = [
                [
                    'Créateur',
                    strval($request->getCreator()),
                ],
                [
                    'Date de création',
                    $this->getDate($request->getCreatedAt()),
                ],
                [
                    'Date de modification',
                    $this->getDate($request->getUpdatedAt()),
                ],
            ];

            $section->addTitle('Demande', 3);
            $this->addTable($section, $requestData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Demandeur', 3);
            $this->addTable($section, $applicantData, true, self::TABLE_ORIENTATION_VERTICAL);

            // Only display concerned people if applicant isn't concerned one
            if (!$applicant->isConcernedPeople()) {
                $section->addTitle('Personne concernée', 3);
                $this->addTable($section, $concernedPeopleData, true, self::TABLE_ORIENTATION_VERTICAL);
            }

            $section->addTitle('Réponse', 3);
            $this->addTable($section, $responseData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Historique', 3);
            $this->addTable($section, $historyData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }
}
