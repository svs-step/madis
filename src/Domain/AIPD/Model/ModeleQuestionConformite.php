<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("none")
 */
class ModeleQuestionConformite extends AbstractQuestionConformite
{
    /**
     * @Serializer\Exclude
     */
    private ModeleAnalyse $modeleAnalyse;

    public function __construct(string $question, int $position, ?ModeleAnalyse $modeleAnalyse = null)
    {
        parent::__construct($question, $position);

        if (!is_null($modeleAnalyse)) {
            $this->modeleAnalyse = $modeleAnalyse;
        }
    }

    public function getModeleAnalyse(): ModeleAnalyse
    {
        return $this->modeleAnalyse;
    }

    public function setModeleAnalyse(ModeleAnalyse $modeleAnalyse): void
    {
        $this->modeleAnalyse = $modeleAnalyse;
    }
}
