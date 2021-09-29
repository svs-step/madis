<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class VraisemblanceGraviteDictionary extends SimpleDictionary
{
    const NEGLIGEABLE = 'negligable';
    const LIMITEE     = 'limitee';
    const IMPORTANTE  = 'importante';
    const MAXIMALE    = 'maximale';

    public function __construct()
    {
        parent::__construct('vraisemblance_gravite', self::getVraisemblanceGravite());
    }

    public function getVraisemblanceGravite()
    {
        return [
            self::NEGLIGEABLE => 'Négligeable',
            self::LIMITEE     => 'Limitée',
            self::IMPORTANTE  => 'Importante',
            self::MAXIMALE    => 'Maximale',
        ];
    }
}
