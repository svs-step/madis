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
use App\Domain\Registry\Model\Proof;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpWord\Element\Section;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProofGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
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
            $entityManager
        );

        $this->translator = $translator;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addSyntheticView(Section $section, array $data, bool $forOverviewReport = false): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function addDetailedView(Section $section, array $data): void
    {

    }

    public function Prooflist(Section $section)
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $proofs = $this->entityManager->getRepository(Proof::class)->findBy(['collectivity' => $collectivity]);

        $section->addTitle('Liste des preuves',2);
        $proofAnnexListTable = $section->addTable($this->tableStyle);
        $proofAnnexListTable->addRow(null, array('tblHeader' => true, 'cantsplit' => true));
        $cell = $proofAnnexListTable->addCell(2500, $this->cellHeadStyle);
        $cell->addText('Nom du document', $this->textHeadStyle);
        $cell = $proofAnnexListTable->addCell(2500, $this->cellHeadStyle);
        $cell->addText('Type de document', $this->textHeadStyle);
        $cell = $proofAnnexListTable->addCell(2500, $this->cellHeadStyle);
        $cell->addText('Date de dépôt', $this->textHeadStyle);


        foreach($proofs as $item){
            $proofAnnexListTable->addRow(null, ['cantsplit' => true]);
            $cell = $proofAnnexListTable->addCell(2500);
            $cell->addText($item->getName());
            $cell = $proofAnnexListTable->addCell(2500);
            $cell->addText($item->getType());
            $cell = $proofAnnexListTable->addCell(2500);
            $cell->addText($item->getCreatedAt('d/m/Y'));
        }
    }
}
