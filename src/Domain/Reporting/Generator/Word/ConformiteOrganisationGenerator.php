<?php

namespace App\Domain\Reporting\Generator\Word;

use App\Domain\Registry\Dictionary\ConformiteOrganisation\ReponseDictionary;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Service\ConformiteOrganisationService;
use App\Domain\User\Dictionary\ContactCivilityDictionary;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\ListItem;

class ConformiteOrganisationGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    protected $average;

    public function addSyntheticView(Section $section, array $data): void
    {
        /*  Not used anymore in this generator since it's useless
            All content is generated in the addDetailedView() method below */
    }

    public function addDetailedView(Section $section, array $data): void
    {
        /** @var Evaluation $evaluation */
        $evaluation = $data[0];

        /* ////////////////////////////////////////////////////////////// */
        $section->addTitle('Contexte', 1);
        $contextData = [
            [
                'Date de l\'évaluation',
                $evaluation->getDate()->format('Y-m-d'),
            ],
            [
                'Liste des participants',
                [['array' => $this->getFormattedParticipants($evaluation)]],
            ],
        ];
        $this->addTable($section, $contextData, false, self::TABLE_ORIENTATION_VERTICAL);

        /* ////////////////////////////////////////////////////////////// */
        $section->addTitle('Liste des processus', 1);

        $orderedConformites = ConformiteOrganisationService::getOrderedConformites($evaluation);
        $tableData          = $this->getConformitesTable($orderedConformites);

        $this->addTable($section, $tableData, false, self::TABLE_ORIENTATION_HORIZONTAL);

        /* ////////////////////////////////////////////////////////////// */
        $section->addPageBreak();
        $section->addTitle('Détail des processus', 1);

        /** @var Conformite $conformite */
        foreach ($orderedConformites as $key => $conformite) {
            if (0 != $key) {
                $section->addPageBreak();
            }

            $processus = [];

            $section->addTitle($conformite->getProcessus()->getNom(), 3);
            $section->addText($conformite->getProcessus()->getDescription());

            foreach (ConformiteOrganisationService::getOrderedReponse($conformite) as $reponse) {
                $processus[] = [
                    \strip_tags($reponse->getQuestion()->getNom()),
                    $this->getFormattedReponse($reponse),
                ];
            }
            $actions        = \iterable_to_array($conformite->getActionProtections());
            $withAllActions = $data[1]; /* Useless, for reading purpose only */
            if (!$withAllActions) {
                $actions = $conformite->getNonAppliedActionProtections();
            }

            $actions = !empty(\iterable_to_array($conformite->getActionProtections()))
                ? $this->getFormattedActionsDeProtection($actions)
                : 'Aucune';
            $processus[] = [
                'Actions de protection',
                $actions,
            ];

            $this->addTable($section, $processus, false, self::TABLE_ORIENTATION_VERTICAL);
        }
    }

    public function addGlobalOverview(Section $section, ?Evaluation $evaluation = null)
    {
        if (null === $evaluation) {
            return;
        }

        $conformites = ConformiteOrganisationService::getOrderedConformites($evaluation);

        $scores = [];
        foreach ($conformites as $conformite) {
            $scores[] = $conformite->getConformite();
        }

        $style = [
            'width'            => Converter::cmToEmu(15),
            'height'           => Converter::cmToEmu(11),
            'showAxisLabels'   => true,
            'showGridY'        => true,
            'dataLabelOptions' => [
                'showVal'     => false,
                'showCatName' => false,
            ],
        ];

        $section->addTitle('Analyse de la conformité de la structure', 2);
        $listStyle = ['listType' => ListItem::TYPE_NUMBER_NESTED];
        $section->addText('Afin de répondre aux objectifs du RGPD, la gestion des DCP est structurée en 12 processus dont les objectifs sont définis dans le document figurant en annexe et à valeur de preuve.');
        $section->addText('Chacun des 12 processus est évalué annuellement et selon l’échelle de maturité ci-après.');
        $section->addText('Lorsque cela est nécessaire une ou plusieurs actions de progrès sont proposées au responsable du traitement pour validation et mise en opération.');
        $section->addText('Liste des 12 processus :');
        $section->addListItem('Organiser la conformité', 0, null, $listStyle);
        $section->addListItem('Gérer les exigences et les poursuites', 0, null, $listStyle);
        $section->addListItem('Gérer les sous-traitants de DCP', 0, null, $listStyle);
        $section->addListItem('Évaluer et auditer', 0, null, $listStyle);
        $section->addListItem('Gérer les traitements', 0, null, $listStyle);
        $section->addListItem('Sensibiliser, former, communiquer', 0, null, $listStyle);
        $section->addListItem('Gérer les risques et les impacts sur la vie privée', 0, null, $listStyle);
        $section->addListItem('Gérer les droits des personnes concernées', 0, null, $listStyle);
        $section->addListItem('Gérer les violations de DCP', 0, null, $listStyle);
        $section->addListItem('Gérer la protection des DCP', 0, null, $listStyle);
        $section->addListItem('Gérer la documentation et les preuves', 0, null, $listStyle);
        $section->addListItem('Gérer les opérations du SMDCP', 0, null, $listStyle);

        $this->average = 0;
        $tableData     = $this->getConformitesTable($conformites);

        $section->addText('Le graphique représente la situation au ' . date('d/m/Y') . '. Sur l’ensemble des processus, la moyenne est de ' . round($this->average, 2) . '/5. À chaque processus, des propositions d’améliorations sont faites puis retranscrites dans le plan de progrès.');

        $section->addChart('column', $this->extractConformiteProcessus($evaluation), $scores, $style);

        $tableStyleConformite = [
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 100,
            'unit'        => TblWidth::PERCENT,
            'width'       => 100 * 50,
        ];
        $tableOrganisationConformite = $section->addTable($tableStyleConformite);
        $tableOrganisationConformite->addRow(null, ['tblHeader' => true, 'cantsplit' => true]);
        $cell = $tableOrganisationConformite->addCell(2000, $this->cellHeadStyle);
        $cell->addText('Pilote', $this->textHeadStyle);
        $cell = $tableOrganisationConformite->addCell(4500, $this->cellHeadStyle);
        $cell->addText('Processus', $this->textHeadStyle);
        $cell = $tableOrganisationConformite->addCell(1500, $this->cellHeadStyle);
        $cell->addText('Conformité', $this->textHeadStyle);

        foreach ($tableData as $line) {
            $tableOrganisationConformite->addRow(null, ['cantsplit' => true]);
            $cell1 = $tableOrganisationConformite->addCell(2000);
            $cell1->addText($line[0]);
            $cell2 = $tableOrganisationConformite->addCell(4500);
            $cell2->addText($line[1]);
            $cell3 = $tableOrganisationConformite->addCell(1500, ['align' => 'center', 'bgColor' => $line[2]['style']['bgColor']]);
            $cell3->addText($line[2]['content']['text'], ['bold' => true], ['align' => 'center']);
        }
    }

    private function getConformitesTable(array $conformites): array
    {
        $tableData = [];
        foreach ($conformites as $conformite) {
            $this->average += $conformite->getConformite();
            switch (true) {
                case $conformite->getConformite() < 2.5:
                    $bgColor = 'ffa7a7';
                    break;
                case $conformite->getConformite() < 3.5:
                    $bgColor = 'fac9ad';
                    break;
                default:
                    $bgColor = 'bce292';
                    break;
            }
            $tableData[] = [
                $conformite->getPilote() ?? 'Inexistant',
                $conformite->getProcessus()->getNom(),
                ['content' => ['text' => $conformite->getConformite()], 'style' => ['bgColor' => $bgColor, 'bold' => true]],
            ];
        }
        $this->average /= 12;

        return $tableData;
    }

    private function extractConformiteProcessus(Evaluation $evaluation): array
    {
        $processus = [];
        foreach (ConformiteOrganisationService::getOrderedConformites($evaluation) as $conformite) {
            $processus[] = $conformite->getProcessus()->getNom();
        }

        return $processus;
    }

    private function getFormattedReponse(Reponse $reponse): string
    {
        switch ($reponse->getReponse()) {
            case null:
                return 'Inexistant';
            case ReponseDictionary::NON_CONCERNE:
                return
                    ReponseDictionary::getReponseLabelFromKey($reponse->getReponse()) .
                    ' (' . $reponse->getReponseRaison() . ')';
            default:
                return ReponseDictionary::getReponseLabelFromKey($reponse->getReponse());
        }
    }

    private function getFormattedParticipants(Evaluation $evaluation): array
    {
        $participants = [];
        foreach ($evaluation->getParticipants() as $participant) {
            $participantsString = '';
            $participantsString .= ContactCivilityDictionary::getCivilities()[$participant->getCivilite()];
            $participantsString .= ' ' . $participant->getPrenom() . ' ' . $participant->getNomDeFamille();
            if (null !== $participant->getFonction()) {
                $participantsString .= ', ' . $participant->getFonction();
            }
            $participants[] = $participantsString;
        }

        return $participants;
    }

    private function getFormattedActionsDeProtection(array $actions)
    {
        $formattedActions = [];
        /** @var Mesurement $action */
        foreach ($actions as $action) {
            $formattedActions[] = $action->getName();
        }

        return $formattedActions;
    }
}
