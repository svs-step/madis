<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VraisemblanceGraviteDictionary extends SimpleDictionary
{
    const NEGLIGEABLE = 'negligeable';
    const LIMITEE     = 'limitee';
    const IMPORTANTE  = 'importante';
    const MAXIMALE    = 'maximale';

    public function __construct(string $name = 'vraisemblance_gravite', array $values = [])
    {
        if (empty($values)) {
            $values = self::getVraisemblanceGravite();
        }
        parent::__construct($name, $values);
    }

    public static function getVraisemblanceGravite(): array
    {
        return [
            self::NEGLIGEABLE => 'Négligeable',
            self::LIMITEE     => 'Limitée',
            self::IMPORTANTE  => 'Importante',
            self::MAXIMALE    => 'Maximale',
        ];
    }

    public static function getMasculineValues()
    {
        return [
            self::NEGLIGEABLE => 'Négligeable',
            self::LIMITEE     => 'Limité',
            self::IMPORTANTE  => 'Important',
            self::MAXIMALE    => 'Maximal',
        ];
    }

    public static function getPoidsFromImpact(string $key): int
    {
        if (array_key_exists($key, array_keys(self::getVraisemblanceGravite()))) {
            throw new NotFoundHttpException('Key ' . $key . ' not found in VraisemblanceGraviteDictionary');
        }

        switch ($key) {
            case self::NEGLIGEABLE:
                return 1;
            case self::LIMITEE:
                return 2;
            case self::IMPORTANTE:
                return 3;
            default:
                return 4;
        }
    }

    public static function getImpact(int $poids): string
    {
        if ($poids < 1 || $poids > 4) {
            throw new NotFoundHttpException('No values for poids ' . $poids . ' has been found in VraisemblanceGraviteDictionary');
        }
        switch ($poids) {
            case 1:
                return self::NEGLIGEABLE;
            case 2:
                return self::LIMITEE;
            case 3:
                return self::IMPORTANTE;
            default:
                return self::MAXIMALE;
        }
    }
}
