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
use App\Domain\Registry\Form\Type\RequestType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RequestController extends CRUDController
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

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
        Repository\Request $repository,
        RequestStack $requestStack,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->requestStack         = $requestStack;
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
        return 'request';
    }

    protected function getModelClass(): string
    {
        return Model\Request::class;
    }

    protected function getFormType(): string
    {
        return RequestType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        $request   = $this->requestStack->getMasterRequest();
        $archived  = 'false' === $request->query->get('archive') || \is_null($request->query->get('archive'))
            ? false
            : true
        ;

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->repository->findAllArchived($archived);
        }

        return $this->repository->findAllArchivedByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            $archived
        );
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
