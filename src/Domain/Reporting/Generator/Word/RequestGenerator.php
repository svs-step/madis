<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Reporting\Generator\Word;

use App\Domain\Registry\Dictionary\RequestAnswerTypeDictionary;
use App\Domain\Registry\Dictionary\RequestCivilityDictionary;
use App\Domain\Registry\Dictionary\RequestObjectDictionary;
use PhpOffice\PhpWord\Element\Section;

class RequestGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
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
                $this->getDate($request->getDate(), 'd/m/Y'),
                RequestObjectDictionary::getObjects()[$request->getObject()],
                $this->getDate($request->getAnswer()->getDate(), 'd/m/Y'),
            ];
        }

        $section->addTitle('Registre des demandes de personnes concernées', 2);
        $section->addText("Un registre des demandes des personnes concernées est tenu à jour par '{$collectivity}'.");
        $section->addText("Il y a eu {$nbTotal} demandes des personnes concernées.");

        if (0 < $nbTotal) {
            $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
        }
    }

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
        $nbTotal = \count($data);
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

    public function addDetailedView(Section $section, array $data): void
    {
        $section->addTitle('Détail des demandes', 1);

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
                    'Autre demande',
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

            $historyData = [
                [
                    'Créateur',
                    $request->getCreator(),
                ],
                [
                    'Date de création',
                    $this->getDate($request->getCreatedAt()),
                ],
                [
                    'Dernière mise à jour',
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
