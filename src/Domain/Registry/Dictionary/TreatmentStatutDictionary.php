<?php

declare(strict_types=1);

namespace App\Domain\Registry\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class TreatmentStatutDictionary extends SimpleDictionary
{
    public const DRAFT    = 'draft';
    public const FINISHED = 'finished';
    public const CHECKED  = 'checked';

    public function __construct()
    {
        parent::__construct('treatment_statut', $this->getStatuts());
    }

    public static function getStatuts()
    {
        return [
            self::DRAFT    => 'Brouillon',
            self::FINISHED => 'Terminé',
            self::CHECKED  => 'Contrôlé',
        ];
    }
}
