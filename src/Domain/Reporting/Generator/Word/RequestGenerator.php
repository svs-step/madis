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
        // TODO
    }

    public function addDetailedView(Section $section, array $data): void
    {
        // TODO
    }
}
