<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class ModeleQuestionConformite extends AbstractQuestionConformite
{
    private ModeleAnalyse $modeleAnalyse;

    public function __construct(string $question, int $position, ?ModeleAnalyse $modeleAnalyse = null)
    {
        parent::__construct($question, $position);

        if (!is_null($modeleAnalyse)) {
            $this->modeleAnalyse = $modeleAnalyse;
        }
    }
}
