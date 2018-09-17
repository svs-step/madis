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

namespace App\Tests\Domain\Maturity\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Maturity\Controller\SurveyController;
use App\Domain\Maturity\Form\Type\SurveyType;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SurveyControllerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var EntityManagerInterface
     */
    private $managerProphecy;

    /**
     * @var TranslatorInterface
     */
    private $translatorProphecy;

    /**
     * @var Repository\Survey
     */
    private $repositoryProphecy;

    /**
     * @var Repository\Question
     */
    private $questionRepositoryProphecy;

    /**
     * @var WordHandler
     */
    private $wordHandlerProphecy;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authenticationCheckerProphecy;

    /**
     * @var UserProvider
     */
    private $userProviderProphecy;

    /**
     * @var SurveyController
     */
    private $controller;

    public function setUp()
    {
        $this->managerProphecy                = $this->prophesize(EntityManagerInterface::class);
        $this->translatorProphecy             = $this->prophesize(TranslatorInterface::class);
        $this->repositoryProphecy             = $this->prophesize(Repository\Survey::class);
        $this->questionRepositoryProphecy     = $this->prophesize(Repository\Question::class);
        $this->wordHandlerProphecy            = $this->prophesize(WordHandler::class);
        $this->authenticationCheckerProphecy  = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->userProviderProphecy           = $this->prophesize(UserProvider::class);

        $this->controller = new SurveyController(
            $this->managerProphecy->reveal(),
            $this->translatorProphecy->reveal(),
            $this->repositoryProphecy->reveal(),
            $this->questionRepositoryProphecy->reveal(),
            $this->wordHandlerProphecy->reveal(),
            $this->authenticationCheckerProphecy->reveal(),
            $this->userProviderProphecy->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(CRUDController::class, $this->controller);
    }

    public function testGetDomain()
    {
        $this->assertEquals(
            'maturity',
            $this->invokeMethod($this->controller, 'getDomain', [])
        );
    }

    public function testGetModel()
    {
        $this->assertEquals(
            'survey',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Survey::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            SurveyType::class,
            $this->invokeMethod($this->controller, 'getFormType', [])
        );
    }

    /**
     * Test getListData
     * User is granted ROLE_ADMIN.
     */
    public function testGetListDataForRoleGranted()
    {
        $valueReturnedByRepository = ['dummyValues'];
        $order                     = [
            'createdAt' => 'DESC',
        ];

        // Granted
        $this->authenticationCheckerProphecy
            ->isGranted('ROLE_ADMIN')
            ->shouldBeCalled()
            ->willReturn(true)
        ;

        // No need to restrict query to collectivity
        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldNotBeCalled()
        ;

        // findAll must be called but not findAllByCollectivity
        $this->repositoryProphecy
            ->findAll($order)
            ->shouldBeCalled()
            ->willReturn($valueReturnedByRepository)
        ;
        $this->repositoryProphecy
            ->findAllByCollectivity(Argument::cetera())
            ->shouldNotBeCalled()
        ;

        $this->assertEquals(
            $valueReturnedByRepository,
            $this->invokeMethod($this->controller, 'getListData')
        );
    }

    /**
     * Test getListData
     * User is not granted ROLE_ADMIN.
     */
    public function testGetListDataForRoleNotGranted()
    {
        $valueReturnedByRepository = ['dummyValues'];
        $order                     = [
            'createdAt' => 'DESC',
        ];

        // Not granted
        $this->authenticationCheckerProphecy
            ->isGranted('ROLE_ADMIN')
            ->shouldBeCalled()
            ->willReturn(false)
        ;

        $collectivity = $this->prophesize(Collectivity::class)->reveal();
        $userProphecy = $this->prophesize(User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldBeCalled()
            ->willReturn($userProphecy->reveal())
        ;

        // findAllByCollectivity must be called but not findAll
        $this->repositoryProphecy
            ->findAllByCollectivity($collectivity, $order)
            ->shouldBeCalled()
            ->willReturn($valueReturnedByRepository)
        ;
        $this->repositoryProphecy
            ->findAll()
            ->shouldNotBeCalled()
        ;

        $this->assertEquals(
            $valueReturnedByRepository,
            $this->invokeMethod($this->controller, 'getListData')
        );
    }

    /**
     * Test reportAction.
     * Has a previous data.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function testReportAction()
    {
        $id       = 'foo';
        $newData  = $this->prophesize(Model\Survey::class)->reveal();
        $oldData  = $this->prophesize(Model\Survey::class)->reveal();
        $response = $this->prophesize(BinaryFileResponse::class)->reveal();

        $data = [
            'new' => $newData,
            'old' => $oldData,
        ];

        $this->repositoryProphecy->findOneById($id)->shouldBeCalled()->willReturn($newData);
        $this->repositoryProphecy->findPreviousById($id, 1)->shouldBeCalled()->willReturn([$oldData]);

        $this->wordHandlerProphecy
            ->generateMaturitySurveyReport($data)
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals(
            $response,
            $this->controller->reportAction($id)
        );
    }

    /**
     * Test reportAction.
     * Hasn't a previous data.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function testReportActionNoPreviousData()
    {
        $id       = 'foo';
        $newData  = $this->prophesize(Model\Survey::class)->reveal();
        $response = $this->prophesize(BinaryFileResponse::class)->reveal();

        $data = [
            'new' => $newData,
        ];

        $this->repositoryProphecy->findOneById($id)->shouldBeCalled()->willReturn($newData);
        $this->repositoryProphecy->findPreviousById($id, 1)->shouldBeCalled()->willReturn([]);

        $this->wordHandlerProphecy
            ->generateMaturitySurveyReport($data)
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals(
            $response,
            $this->controller->reportAction($id)
        );
    }
}
