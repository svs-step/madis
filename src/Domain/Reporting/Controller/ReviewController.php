<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Reporting\Controller;

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Maturity\Repository as MaturityRepository;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ReviewController extends Controller
{
    /**
     * @var WordHandler
     */
    private $wordHandler;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var Repository\Treatment;
     */
    private $treatmentRepository;

    /**
     * @var Repository\Contractor;
     */
    private $contractorRepository;

    /**
     * @var Repository\Mesurement;
     */
    private $mesurementRepository;

    /**
     * @var Repository\Request
     */
    private $requestRepository;

    /**
     * @var MaturityRepository\Survey;
     */
    private $surveyRepository;

    /**
     * @var Repository\Violation
     */
    private $violationRepository;

    public function __construct(
        WordHandler $wordHandler,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        Repository\Treatment $treatmentRepository,
        Repository\Contractor $contractorRepository,
        Repository\Mesurement $mesurementRepository,
        Repository\Request $requestRepository,
        Repository\Violation $violationRepository,
        MaturityRepository\Survey $surveyRepository
    ) {
        $this->wordHandler          = $wordHandler;
        $this->userProvider         = $userProvider;
        $this->authorizationChecker = $authorizationChecker;
        $this->treatmentRepository  = $treatmentRepository;
        $this->contractorRepository = $contractorRepository;
        $this->mesurementRepository = $mesurementRepository;
        $this->requestRepository    = $requestRepository;
        $this->violationRepository  = $violationRepository;
        $this->surveyRepository     = $surveyRepository;
    }

    /**
     * Download an entire review.
     *
     * @param string $id
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws \Exception
     *
     * @return BinaryFileResponse
     */
    public function indexAction(string $id): BinaryFileResponse
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        if (!$collectivity) {
            throw new NotFoundHttpException('No collectivity found');
        }

        $maturity = [];
        $objects  = $this->surveyRepository->findAllByCollectivity($collectivity, ['createdAt' => 'DESC'], 2);

        if (1 <= \count($objects)) {
            $maturity['new'] = $objects[0];
        }
        if (2 <= \count($objects)) {
            $maturity['old'] = $objects[1];
        }

        return $this->wordHandler->generateOverviewReport(
            $this->treatmentRepository->findAllActiveByCollectivity($collectivity),
            $this->contractorRepository->findAllByCollectivity($collectivity),
            $this->mesurementRepository->findAllByCollectivity($collectivity),
            $maturity,
            $this->requestRepository->findAllByCollectivity($collectivity),
            $this->violationRepository->findAllByCollectivity($collectivity)
        );
    }
}
