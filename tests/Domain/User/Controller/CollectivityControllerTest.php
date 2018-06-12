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

namespace App\Tests\Domain\User\Controller;

use App\Application\Controller\CRUDController;
use App\Domain\User\Controller\CollectivityController;
use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;

class CollectivityControllerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var CollectivityController
     */
    private $controller;

    public function setUp()
    {
        $this->controller = new CollectivityController(
            $this->prophesize(EntityManagerInterface::class)->reveal(),
            $this->prophesize(TranslatorInterface::class)->reveal(),
            $this->prophesize(Repository\Collectivity::class)->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(CRUDController::class, $this->controller);
    }

    public function testGetDomain()
    {
        $this->assertEquals(
            'user',
            $this->invokeMethod($this->controller, 'getDomain', [])
        );
    }

    public function testGetModel()
    {
        $this->assertEquals(
            'collectivity',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Collectivity::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            CollectivityType::class,
            $this->invokeMethod($this->controller, 'getFormType', [])
        );
    }
}
