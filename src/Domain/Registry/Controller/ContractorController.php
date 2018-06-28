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

namespace App\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Form\Type\ContractorType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Generator\WordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ContractorController extends CRUDController
{
    /**
     * @var WordGenerator
     */
    protected $wordGenerator;

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
        Repository\Contractor $repository,
        WordGenerator $wordGenerator,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->wordGenerator        = $wordGenerator;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
    }

    protected function getDomain(): string
    {
        return 'registry';
    }

    protected function getModel(): string
    {
        return 'contractor';
    }

    protected function getModelClass(): string
    {
        return Model\Contractor::class;
    }

    protected function getFormType(): string
    {
        return ContractorType::class;
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

    public function reportAction(): Response
    {
        $objects = $this->repository->findAllByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            ['name' => 'asc']
        );

        return $this->wordGenerator->generateRegistryContractorReport($objects);
    }
}
