<?php

namespace App\Tests\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Controller\ConformiteOrganisationController;
use App\Domain\Registry\Repository\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Repository\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Repository\ConformiteOrganisation\Processus;
use App\Domain\Registry\Repository\ConformiteOrganisation\Question;
use App\Domain\Reporting\Handler\WordHandler;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManager;
use Knp\Snappy\Pdf;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConformiteOrganisationControllerTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var ConformiteOrganisationController
     */
    private $controller;

    /**
     * @var Evaluation
     */
    private $evaluationRepository;

    /**
     * @var Question|ObjectProphecy
     */
    private $questionRepository;

    /**
     * @var Processus|ObjectProphecy
     */
    private $processusRepository;

    /**
     * @var Conformite|ObjectProphecy
     */
    private $conformiteRepository;

    /**
     * @var UserProvider|ObjectProphecy
     */
    private $userProvider;

    /**
     * @var EntityManager|ObjectProphecy
     */
    private $entityManager;

    /**
     * @var TranslatorInterface|ObjectProphecy
     */
    private $translator;

    /**
     * @var AuthorizationCheckerInterface|ObjectProphecy
     */
    private $authorisationChecker;

    /**
     * @var EventDispatcherInterface|ObjectProphecy
     */
    private $dispatcher;

    /**
     * @var WordHandler|ObjectProphecy
     */
    private $wordHandler;

    /**
     * @var Pdf|ObjectProphecy
     */
    private $pdf;

    public function setUp(): void
    {
        $this->entityManager        = $this->prophesize(EntityManager::class);
        $this->translator           = $this->prophesize(TranslatorInterface::class);
        $this->evaluationRepository = $this->prophesize(Evaluation::class);
        $this->questionRepository   = $this->prophesize(Question::class);
        $this->processusRepository  = $this->prophesize(Processus::class);
        $this->conformiteRepository = $this->prophesize(Conformite::class);
        $this->userProvider         = $this->prophesize(UserProvider::class);
        $this->authorisationChecker = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->dispatcher           = $this->prophesize(EventDispatcherInterface::class);
        $this->wordHandler          = $this->prophesize(WordHandler::class);
        $this->pdf                  = $this->prophesize(Pdf::class);

        $this->controller = new ConformiteOrganisationController(
            $this->entityManager->reveal(),
            $this->translator->reveal(),
            $this->evaluationRepository->reveal(),
            $this->questionRepository->reveal(),
            $this->processusRepository->reveal(),
            $this->conformiteRepository->reveal(),
            $this->userProvider->reveal(),
            $this->authorisationChecker->reveal(),
            $this->dispatcher->reveal(),
            $this->wordHandler->reveal(),
            $this->pdf->reveal()
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
            'conformite_organisation',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            \App\Domain\Registry\Model\ConformiteOrganisation\Evaluation::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetListData()
    {
        $returnData = ['foo'];
        $this->evaluationRepository->findAll()->willReturn($returnData);
        $this->assertEquals(
            $returnData,
            $this->invokeMethod($this->controller, 'getListData', [])
        );
    }
}
