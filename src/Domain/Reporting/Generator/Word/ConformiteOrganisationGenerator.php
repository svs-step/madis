<?php

namespace App\Domain\Reporting\Generator\Word;

use App\Domain\Registry\Dictionary\ConformiteOrganisation\ReponseDictionary;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
use App\Domain\User\Dictionary\ContactCivilityDictionary;
use PhpOffice\PhpWord\Element\Section;

class ConformiteOrganisationGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    public function addSyntheticView(Section $section, array $data): void
    {
        $section->addTitle('Liste des processus', 1);

        $tableData = [
            [
                'Pilote',
                'Processus',
                'Conformité',
            ],
        ];

        foreach ($data as $conformite) {
            $tableData[] = [
                $conformite->getPilote(),
                $conformite->getProcessus()->getNom(),
                $conformite->getConformite(),
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

    public function addDetailedView(Section $section, array $data): void
    {
        $section->addPageBreak();
        $section->addTitle('Détail des processus', 1);

        /** @var Evaluation $evaluation */
        $evaluation  = $data[0];
        $conformites = \iterable_to_array($evaluation->getConformites());
        usort($conformites, function ($a, $b) {
            return $a->getProcessus()->getPosition() > $b->getProcessus()->getPosition() ? 1 : -1;
        });
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

            $this->addTable($section, $processus, true, self::TABLE_ORIENTATION_VERTICAL);
        }

        $historyData = [
            [
                'Dernière évaluation',
                $evaluation->getDate(),
            ],
            [
                'Liste des participants',
                [['array' => $this->getFormattedParticipants($evaluation)]],
            ],
        ];
        $section->addTitle('Historique', 1);
        $this->addTable($section, $historyData, true, self::TABLE_ORIENTATION_VERTICAL);
    }

    private function getFormattedReponse(Reponse $reponse): string
    {
        switch ($reponse->getReponse()) {
            case null:
                return '';
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
}
