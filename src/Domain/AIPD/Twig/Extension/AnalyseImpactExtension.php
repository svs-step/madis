<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AnalyseImpactExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getLastAipd', [$this, 'getLastAipd']),
        ];
    }

    public function getLastAipd()
    {
    }
}
