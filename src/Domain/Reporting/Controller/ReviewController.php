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

namespace App\Domain\Reporting\Controller;

use App\Application\Symfony\Security\UserProvider;
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

    public function __construct(
        WordHandler $wordHandler,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        Repository\Treatment $treatmentRepository,
        Repository\Contractor $contractorRepository
    ) {
        $this->wordHandler          = $wordHandler;
        $this->userProvider         = $userProvider;
        $this->authorizationChecker = $authorizationChecker;
        $this->treatmentRepository  = $treatmentRepository;
        $this->contractorRepository = $contractorRepository;
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

        return $this->wordHandler->generateOverviewReport(
            $this->treatmentRepository->findAllActiveByCollectivity($collectivity),
            $this->contractorRepository->findAllByCollectivity($collectivity)
        );
    }
}
