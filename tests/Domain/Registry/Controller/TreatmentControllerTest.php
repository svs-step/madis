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

namespace App\Tests\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Domain\Registry\Controller\TreatmentController;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;

class TreatmentControllerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var TreatmentController
     */
    private $controller;

    public function setUp()
    {
        $this->controller = new TreatmentController(
            $this->prophesize(EntityManagerInterface::class)->reveal(),
            $this->prophesize(TranslatorInterface::class)->reveal(),
            $this->prophesize(Repository\Treatment::class)->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(CRUDController::class, $this->controller);
    }

    public function testGetDomain()
    {
        $this->assertEquals(
            'registry',
            $this->invokeMethod($this->controller, 'getDomain', [])
        );
    }

    public function testGetModel()
    {
        $this->assertEquals(
            'treatment',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Treatment::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            TreatmentType::class,
            $this->invokeMethod($this->controller, 'getFormType', [])
        );
    }
}
