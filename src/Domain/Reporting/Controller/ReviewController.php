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

namespace App\Domain\Reporting\Controller;

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Maturity\Repository as MaturityRepository;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ReviewController extends AbstractController
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
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws \Exception
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
            $this->requestRepository->findAllArchivedByCollectivity($collectivity, false),
            $this->violationRepository->findAllArchivedByCollectivity($collectivity, false)
        );
    }
}
