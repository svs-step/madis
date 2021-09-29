<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class ModeleQuestionConformite extends AbstractQuestionConformite
{
    private ModeleAnalyse $modeleAnalyse;

    public function __construct(string $question, ?ModeleAnalyse $modeleAnalyse = null)
    {
        parent::__construct($question);

        if (!is_null($modeleAnalyse)) {
            $this->modeleAnalyse = $modeleAnalyse;
        }
    }
}
