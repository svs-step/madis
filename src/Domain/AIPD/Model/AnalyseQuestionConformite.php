<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use App\Domain\Registry\Model\ConformiteTraitement\Reponse;

class AnalyseQuestionConformite extends AbstractQuestionConformite
{
    protected AnalyseImpact $analyseImpact;

    protected ?string $justificatif;

    protected Reponse $reponseConformite;

    public function __construct(string $question, int $position)
    {
        parent::__construct($question, $position);
    }

    public function getAnalyseImpact(): AnalyseImpact
    {
        return $this->analyseImpact;
    }

    public function setAnalyseImpact(AnalyseImpact $analyseImpact): void
    {
        $this->analyseImpact = $analyseImpact;
    }

    public function getJustificatif(): ?string
    {
        return $this->justificatif;
    }

    public function setJustificatif(?string $justificatif): void
    {
        $this->justificatif = $justificatif;
    }

    public function getReponseConformite(): Reponse
    {
        return $this->reponseConformite;
    }

    public function setReponseConformite(Reponse $reponseConformite): void
    {
        $this->reponseConformite = $reponseConformite;
    }
}
