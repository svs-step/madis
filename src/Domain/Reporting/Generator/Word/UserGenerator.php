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

use App\Application\Symfony\Security\UserProvider;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpWord\Element\Section;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    protected TranslatorInterface $translator;
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserProvider $userProvider,
        ParameterBagInterface $parameterBag,
        TranslatorInterface $translator
    ) {
        parent::__construct(
            $userProvider,
            $parameterBag,
        );

        $this->translator    = $translator;
        $this->entityManager = $entityManager;
    }

    public function addSyntheticView(Section $section, array $data, bool $forOverviewReport = false): void
    {
    }

    public function addDetailedView(Section $section, array $data): void
    {
    }

    public function Userlist(Section $section)
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $users = $this->entityManager->getRepository(User::class)->findBy(['collectivity' => $collectivity]);

        $section->addTitle('Liste des utilisateurs', 2);
        $userAnnexListTable = $section->addTable($this->tableStyle);
        $userAnnexListTable->addRow(null, ['tblHeader' => true, 'cantsplit' => true]);
        $cell = $userAnnexListTable->addCell(1500, $this->cellHeadStyle);
        $cell->addText('Prénom', $this->textHeadStyle);
        $cell = $userAnnexListTable->addCell(1500, $this->cellHeadStyle);
        $cell->addText('Nom', $this->textHeadStyle);
        $cell = $userAnnexListTable->addCell(3000, $this->cellHeadStyle);
        $cell->addText('Email', $this->textHeadStyle);
        $cell = $userAnnexListTable->addCell(1000, $this->cellHeadStyle);
        $cell->addText('Actif', $this->textHeadStyle);

        foreach ($users as $item) {
            $userAnnexListTable->addRow(400, ['exactHeight' => true, 'cantsplit' => true]);
            $cell = $userAnnexListTable->addCell(1500);
            $cell->addText($item->getFirstName());
            $cell = $userAnnexListTable->addCell(1500);
            $cell->addText($item->getLastName());
            $cell = $userAnnexListTable->addCell(3000);
            $cell->addText($item->getEmail());
            $cell = $userAnnexListTable->addCell(1000, ['bgColor' => $item->IsEnabled() ? 'bce292' : 'ffa7a7']);
            $cell->addText($item->IsEnabled() ? 'Actif' : 'Inactif', ['bold' => true]);
        }
    }
}
