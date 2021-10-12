<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\AIPD\Model\CriterePrincipeFondamental;

class BaseCriterePrincipeFondamental extends SimpleDictionary
{
    const PORTE_TRAITEMENT          = 'porte_traitement';
    const CONTEXTE_TRAITEMENT       = 'contexte_traitement';
    const CONFORMITE_CODE           = 'conformite_code';
    const DESCRIPTION_FONCTIONNELLE = 'description_fonctionnelle';
    const IDENTIFICATION_BIENS      = 'identification_biens';

    public function __construct()
    {
        parent::__construct('base_critere_principe_fondamental', $this->getBaseCritere());
    }

    public static function getBaseCritere()
    {
        return [
            self::PORTE_TRAITEMENT          => new CriterePrincipeFondamental('Portée du traitement'),
            self::CONTEXTE_TRAITEMENT       => new CriterePrincipeFondamental('Contexte du traitement'),
            self::CONFORMITE_CODE           => new CriterePrincipeFondamental('Conformité à un code de conduite existant'),
            self::DESCRIPTION_FONCTIONNELLE => new CriterePrincipeFondamental('Description fonctionnelle du traitement'),
            self::IDENTIFICATION_BIENS      => new CriterePrincipeFondamental('Identification des biens'),
        ];
    }
}
