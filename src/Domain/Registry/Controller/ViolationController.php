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

namespace App\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Form\Type\ViolationType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ViolationController extends CRUDController
{
    /**
     * @var WordHandler
     */
    protected $wordHandler;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Violation $repository,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->wordHandler          = $wordHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
    }

    protected function getDomain(): string
    {
        return 'registry';
    }

    protected function getModel(): string
    {
        return 'violation';
    }

    protected function getModelClass(): string
    {
        return Model\Violation::class;
    }

    protected function getFormType(): string
    {
        return ViolationType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->repository->findAll();
        }

        return $this->repository->findAllByCollectivity($this->userProvider->getAuthenticatedUser()->getCollectivity());
    }

    protected function isSoftDelete(): bool
    {
        return true;
    }

    /**
     * Generate a word report of contractors.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     *
     * @return Response
     */
    public function reportAction(): Response
    {
        /*
        $objects = $this->repository->findAllByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            ['name' => 'asc']
        );

        return $this->wordHandler->generateRegistryContractorReport($objects);
        */
    }
}
