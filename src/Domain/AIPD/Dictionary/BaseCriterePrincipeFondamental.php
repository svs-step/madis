<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\AIPD\Model\CriterePrincipeFondamental;

class BaseCriterePrincipeFondamental extends SimpleDictionary
{
    const PORTE_TRAITEMENT          = 'portee_traitement';
    const IDENTIFICATION_BIENS      = 'identification_biens';
    const DESCRIPTION_FONCTIONNELLE = 'description_fonctionnelle';
    const CONFORMITE_CODE           = 'conformite_code';
    const CONTEXTE_TRAITEMENT       = 'contexte_traitement';

    public function __construct()
    {
        parent::__construct('base_critere_principe_fondamental', $this->getBaseCritere());
    }

    public static function getBaseCritere()
    {
        return [
            self::PORTE_TRAITEMENT          => new CriterePrincipeFondamental('Portée du traitement', self::PORTE_TRAITEMENT),
            self::IDENTIFICATION_BIENS      => new CriterePrincipeFondamental('Identification des biens', self::IDENTIFICATION_BIENS),
            self::DESCRIPTION_FONCTIONNELLE => new CriterePrincipeFondamental('Description fonctionnelle du traitement', self::DESCRIPTION_FONCTIONNELLE),
            self::CONFORMITE_CODE           => new CriterePrincipeFondamental('Conformité à un code de conduite existant', self::CONFORMITE_CODE),
            self::CONTEXTE_TRAITEMENT       => new CriterePrincipeFondamental('Contexte du traitement', self::CONTEXTE_TRAITEMENT),
        ];
    }
}
