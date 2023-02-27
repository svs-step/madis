<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Tests\Domain\Documentation\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Documentation\Controller\CategoryController;
use App\Domain\Documentation\Controller\DocumentController;
use App\Domain\Documentation\Form\Type\DocumentType;
use App\Domain\Documentation\Model;
use App\Domain\Documentation\Repository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Gaufrette\FilesystemInterface;
use Knp\Snappy\Pdf;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentControllerTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var EntityManagerInterface
     */
    private $managerProphecy;

    /**
     * @var TranslatorInterface
     */
    private $translatorProphecy;

    /**
     * @var Repository\Document
     */
    private $repositoryProphecy;

    /**
     * @var Repository\Category
     */
    private $categoryRepositoryProphecy;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authenticationCheckerProphecy;

    /**
     * @var UserProvider
     */
    private $userProviderProphecy;

    /**
     * @var Pdf|ObjectProphecy
     */
    private $pdf;

    /**
     * @var CategoryController
     */
    private $controller;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function setUp(): void
    {
        $this->managerProphecy               = $this->prophesize(EntityManagerInterface::class);
        $this->translatorProphecy            = $this->prophesize(TranslatorInterface::class);
        $this->repositoryProphecy            = $this->prophesize(Repository\Document::class);
        $this->categoryRepositoryProphecy    = $this->prophesize(Repository\Category::class);
        $this->authenticationCheckerProphecy = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->userProviderProphecy          = $this->prophesize(UserProvider::class);
        $this->pdf                           = $this->prophesize(Pdf::class);
        $this->documentFilesystem            = $this->prophesize(FilesystemInterface::class);
        $this->thumbFilesystem               = $this->prophesize(FilesystemInterface::class);
        $this->requestStack                  = $this->prophesize(RequestStack::class);
        $this->controller                    = new DocumentController(
            $this->managerProphecy->reveal(),
            $this->translatorProphecy->reveal(),
            $this->repositoryProphecy->reveal(),
            $this->categoryRepositoryProphecy->reveal(),
            $this->authenticationCheckerProphecy->reveal(),
            $this->userProviderProphecy->reveal(),
            $this->documentFilesystem->reveal(),
            $this->thumbFilesystem->reveal(),
            $this->pdf->reveal(),
            $this->requestStack->reveal(),
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(CRUDController::class, $this->controller);
    }

    public function testGetDomain()
    {
        $this->assertEquals(
            'documentation',
            $this->invokeMethod($this->controller, 'getDomain', [])
        );
    }

    public function testGetModel()
    {
        $this->assertEquals(
            'document',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Document::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            DocumentType::class,
            $this->invokeMethod($this->controller, 'getFormType', [])
        );
    }
}
