<?php

declare(strict_types=1);

namespace App\Application\DataFixtures\ORM;

use Faker\Provider\Base as BaseProvider;
use Knp\DictionaryBundle\Dictionary\DictionaryRegistry;

class DictionaryProvider extends BaseProvider
{
    /**
     * @var DictionaryRegistry
     */
    private $dictionaries;

    public function __construct(DictionaryRegistry $dictionaries)
    {
        $this->dictionaries = $dictionaries;
    }

    /**
     * @return mixed
     */
    public function dictionary(string $name)
    {
        return self::randomElement($this->dictionaries->get($name)->getKeys());
    }
}
