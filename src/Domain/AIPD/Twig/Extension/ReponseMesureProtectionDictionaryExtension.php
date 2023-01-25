<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Twig\Extension;

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\AIPD\Dictionary\ReponseMesureProtectionDictionary;
use App\Domain\AIPD\Model\AnalyseImpact;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ReponseMesureProtectionDictionaryExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('reponsedictionary', [$this, 'getDictionary']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('reponsedictionary', [$this, 'getValue']),
        ];
    }

    public function getDictionary(AnalyseImpact $analyse): ?SimpleDictionary
    {
        return new ReponseMesureProtectionDictionary($analyse);
    }

    public function getValue($key, AnalyseImpact $analyse)
    {
        $dictionary = $this->getDictionary($analyse);

        return $dictionary[$key];
    }
}
