<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReponseCritereFondamentalDictionary extends SimpleDictionary
{
    public const REPONSE_NON_RENSEIGNE  = 'non_renseigne';
    public const REPONSE_NON_CONFORME   = 'non_conforme';
    public const REPONSE_CONFORME       = 'conforme';
    public const REPONSE_NON_APPLICABLE = 'non_applicable';

    public function __construct()
    {
        parent::__construct('reponse_critere_fondamental', self::getReponses());
    }

    public static function getReponses()
    {
        return [
            self::REPONSE_NON_RENSEIGNE  => 'Non renseigné',
            self::REPONSE_NON_CONFORME   => 'Non conforme',
            self::REPONSE_CONFORME       => 'Conforme',
            self::REPONSE_NON_APPLICABLE => 'Non applicable',
        ];
    }

    public static function getLabelReponse(string $key): string
    {
        if (!array_key_exists($key, self::getReponses())) {
            throw new NotFoundHttpException('Key ' . $key . ' not found in ReponseCritereFondamentalDictionary');
        }

        switch ($key) {
            case self::REPONSE_NON_RENSEIGNE:
                return 'Pas de réponse';
            default:
                return self::getReponses()[$key];
        }
    }
}
