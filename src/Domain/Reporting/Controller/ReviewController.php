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
use App\Domain\Reporting\Generator\WordGenerator;
use App\Domain\User\Repository as UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ReviewController extends Controller
{
    /**
     * @var WordGenerator
     */
    private $generator;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var UserRepository\Collectivity;
     */
    private $collectivityRepository;

    public function __construct(
        WordGenerator $generator,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        UserRepository\Collectivity $collectivityRepository
    ) {
        $this->generator              = $generator;
        $this->userProvider           = $userProvider;
        $this->authorizationChecker   = $authorizationChecker;
        $this->collectivityRepository = $collectivityRepository;
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
        $collectivity = $this->collectivityRepository->findOneById($id);
        if (!$collectivity) {
            throw new NotFoundHttpException("No collectivity found with ID '{$id}'");
        }

        // Only collectivity users or admin can access to this review
        if ($collectivity !== $this->userProvider->getAuthenticatedUser()->getCollectivity()
        && !$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("You can't access to this collectivity bilan");
        }

        $this->generator->generateHeader();
        $this->generator->generateCollectivitySection($collectivity);

        $date = (new \DateTimeImmutable())->format('Y-m-d_H-i-s');

        return $this->generator->generateResponse("{$collectivity->getName()}-{$date}");
    }
}
