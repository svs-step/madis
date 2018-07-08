<?php
/**
 * Created by PhpStorm.
 * User: bourlard
 * Date: 02/06/2018
 * Time: 11:32.
 */

namespace App\Tests\Domain\Registry\Dictionary;

use App\Domain\Registry\Dictionary\DelayPeriodDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class DelayPeriodDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new DelayPeriodDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new DelayPeriodDictionary();

        $this->assertEquals('registry_delay_period', $roleDictionary->getName());
        $this->assertEquals(DelayPeriodDictionary::getPeriods(), $roleDictionary->getValues());
    }

    public function testGetPeriods()
    {
        $data = [
            DelayPeriodDictionary::PERIOD_DAY   => 'Jour(s)',
            DelayPeriodDictionary::PERIOD_MONTH => 'Mois',
            DelayPeriodDictionary::PERIOD_YEAR  => 'An(s)',
        ];

        $this->assertEquals($data, DelayPeriodDictionary::getPeriods());
    }

    public function testGetPeriodsKeys()
    {
        $data = [
            DelayPeriodDictionary::PERIOD_DAY,
            DelayPeriodDictionary::PERIOD_MONTH,
            DelayPeriodDictionary::PERIOD_YEAR,
        ];

        $this->assertEquals($data, DelayPeriodDictionary::getPeriodsKeys());
    }
}
