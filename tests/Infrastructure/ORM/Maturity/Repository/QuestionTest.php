<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Infrastructure\ORM\Maturity\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository as DomainRepo;
use App\Infrastructure\ORM\Maturity\Repository as InfraRepo;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class QuestionTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var RegistryInterface
     */
    private $registryProphecy;

    /**
     * @var EntityManagerInterface
     */
    private $entityManagerProphecy;

    /**
     * @var InfraRepo\Question
     */
    private $infraRepo;

    public function setUp()
    {
        $this->registryProphecy      = $this->prophesize(RegistryInterface::class);
        $this->entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);

        $this->infraRepo = new InfraRepo\Question($this->registryProphecy->reveal());
    }

    /**
     * Test if repo has good heritage.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(DomainRepo\Question::class, $this->infraRepo);
        $this->assertInstanceOf(CRUDRepository::class, $this->infraRepo);
    }

    /**
     * Test getModelClass.
     *
     * @throws \ReflectionException
     */
    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Question::class,
            $this->invokeMethod($this->infraRepo, 'getModelClass')
        );
    }
}
