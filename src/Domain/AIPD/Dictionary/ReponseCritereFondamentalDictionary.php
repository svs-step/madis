<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class ReponseCritereFondamentalDictionary extends SimpleDictionary
{
    const NON_RENSEIGNE  = 'non_renseigne';
    const NON_CONFORME   = 'non_conforme';
    const CONFORME       = 'conforme';
    const NON_APPLICABLE = 'non_applicable';

    public function __construct()
    {
        parent::__construct('reponse_critere_fondamental', $this->getReponses());
    }

    public function getReponses()
    {
        return [
            self::NON_RENSEIGNE  => 'Non renseigné',
            self::NON_CONFORME   => 'Non conforme',
            self::CONFORME       => 'Conforme',
            self::NON_APPLICABLE => 'Non applicable',
        ];
    }
}
