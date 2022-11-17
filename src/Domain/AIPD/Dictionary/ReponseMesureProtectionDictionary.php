<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\AIPD\Model\AnalyseImpact;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReponseMesureProtectionDictionary extends SimpleDictionary
{
    const INSATISFAISANT      = 'insatisfaisant';
    const BESOIN_AMELIORATION = 'besoin_amelioration';
    const SATISFAISANT        = 'satisfaisant';

    protected AnalyseImpact $aipd;

    public function __construct(AnalyseImpact $aipd)
    {
        parent::__construct('reponse_mesure_protection', self::getReponses($aipd));
        $this->aipd = $aipd;
    }

    public static function getReponses(AnalyseImpact $aipd)
    {
        return [
            self::INSATISFAISANT      => $aipd->getLabelInsatisfaisant() ? $aipd->getLabelInsatisfaisant() : 'Insatisfaisant',
            self::BESOIN_AMELIORATION => $aipd->getLabelAmeliorationPrevue() ? $aipd->getLabelAmeliorationPrevue() : 'Amélioration Prévue',
            self::SATISFAISANT        => $aipd->getLabelSatisfaisant() ? $aipd->getLabelSatisfaisant() : 'Satisfaisant',
        ];
    }

    public static function getPoidsIndexFromReponse(string $reponse, AnalyseImpact $aipd): float
    {
        if (!array_key_exists($reponse, self::getReponses($aipd))) {
            throw new NotFoundHttpException('Key ' . $reponse . ' not found in ReponseMesureProtectionDictionary');
        }

        if (self::SATISFAISANT === $reponse) {
            return 1;
        } elseif (self::BESOIN_AMELIORATION === $reponse) {
            return 0.5;
        }

        return 0;
    }
}
