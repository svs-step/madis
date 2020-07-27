<?php

namespace App\Domain\Reporting\Generator\Word;

use App\Domain\Registry\Dictionary\ConformiteOrganisation\ReponseDictionary;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\User\Dictionary\ContactCivilityDictionary;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;

class ConformiteOrganisationGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    public function addSyntheticView(Section $section, array $data): void
    {
        /*  Not used anymore in this generator since it's useless
            All content is generated in the addDetailedView() method below */
    }

    public function addDetailedView(Section $section, array $data): void
    {
        /** @var Evaluation $evaluation */
        $evaluation  = $data[0];

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
        $this->addTable($section, $contextData, true, self::TABLE_ORIENTATION_VERTICAL);

        /* ////////////////////////////////////////////////////////////// */
        $section->addTitle('Liste des processus', 1);

        $tableData = $this->getConformitesTable($this->getOrderedConformites(\iterable_to_array($evaluation->getConformites())));

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);

        /* ////////////////////////////////////////////////////////////// */
        $section->addPageBreak();
        $section->addTitle('Détail des processus', 1);

        $conformites = $this->getOrderedConformites(\iterable_to_array($evaluation->getConformites()));
        foreach ($conformites as $key => $conformite) {
            if (0 != $key) {
                $section->addPageBreak();
            }

            $processus = [];

            $section->addTitle($conformite->getProcessus()->getNom(), 3);
            $section->addText($conformite->getProcessus()->getDescription());

            foreach ($conformite->getReponses() as $reponse) {
                $processus[] = [
                    $reponse->getQuestion()->getNom(),
                    $this->getFormattedReponse($reponse),
                ];
            }
            $actions = !empty(\iterable_to_array($conformite->getActionProtections()))
                ? $this->getFormattedActionsDeProtection(\iterable_to_array($conformite->getActionProtections()))
                : 'Aucune';
            $processus[] = [
                'Actions de protection',
                $actions,
            ];

            $this->addTable($section, $processus, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }

    public function addGlobalOverview(Section $section, Evaluation $evaluation = null)
    {
        if (null === $evaluation) {
            return;
        }

        $conformites = $this->getOrderedConformites(\iterable_to_array($evaluation->getConformites()));

        $scores = [];
        foreach ($conformites as $conformite) {
            $scores[] = $conformite->getConformite();
        }

        $style = [
            'width'              => Converter::cmToEmu(15),
            'height'             => Converter::cmToEmu(11),
            'showAxisLabels'     => true,
            'showGridY'          => true,
            'dataLabelOptions'   => [
                'showVal'     => false,
                'showCatName' => false,
            ],
        ];

        $section->addTitle('Analyse de la conformité de l\'organisation', 2);

        $section->addChart('column', $this->extractConformiteProcessus($evaluation), $scores, $style);

        $tableData = $this->getConformitesTable($conformites);

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_VERTICAL);
    }

    private function getConformitesTable(array $conformites): array
    {
        $tableData = [
            [
                'Pilote',
                'Processus',
                'Conformité',
            ],
        ];

        foreach ($conformites as $conformite) {
            switch (true) {
                case $conformite->getConformite() < 2.5:
                    $bgColor = 'dd4b39';
                    break;
                case $conformite->getConformite() < 3.5:
                    $bgColor = 'f39c12';
                    break;
                default:
                    $bgColor = '00a65a';
                    break;
            }
            $tableData[] = [
                null === $conformite->getPilote() ? 'Inexistant' : $conformite->getPilote(),
                $conformite->getProcessus()->getNom(),
                ['content' => ['text' => $conformite->getConformite()], 'style' => ['bgColor' => $bgColor, 'bold' => true]],
            ];
        }

        return $tableData;
    }

    private function extractConformiteProcessus(Evaluation $evaluation): array
    {
        $processus = [];
        foreach ($this->getOrderedConformites(\iterable_to_array($evaluation->getConformites())) as $conformite) {
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

    private function getOrderedConformites(array $conformites): array
    {
        usort($conformites, function ($a, $b) {
            return $a->getProcessus()->getPosition() > $b->getProcessus()->getPosition() ? 1 : -1;
        });

        return $conformites;
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
