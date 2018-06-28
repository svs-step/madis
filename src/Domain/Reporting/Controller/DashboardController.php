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
use App\Domain\Registry\Model\Contractor;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserProvider
     */
    private $userProvider;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserProvider $userProvider
    ) {
        $this->entityManager = $entityManager;
        $this->userProvider  = $userProvider;
    }

    public function indexAction()
    {
        $data = [
            'collectivity' => 0,
            'contractor'   => [
                'clauses' => [
                    'yes' => 0,
                    'no'  => 0,
                ],
                'conform' => [
                    'yes' => 0,
                    'no'  => 0,
                ],
            ],
            'user' => 0,
        ];

        $collectivities = $this->entityManager->getRepository(Collectivity::class)->findAll();
        $contractors    = $this->entityManager->getRepository(Contractor::class)->findBy(
            ['collectivity' => $this->userProvider->getAuthenticatedUser()->getCollectivity()]
        );
        $users          = $this->entityManager->getRepository(User::class)->findAll();

        $data['collectivity'] = \count($collectivities);
        foreach ($contractors as $contractor) {
            if ($contractor->isContractualClausesVerified()) {
                ++$data['contractor']['clauses']['yes'];
            } else {
                ++$data['contractor']['clauses']['no'];
            }
            if ($contractor->isConform()) {
                ++$data['contractor']['conform']['yes'];
            } else {
                ++$data['contractor']['conform']['no'];
            }
        }
        $data['user'] = \count($users);

        return $this->render('Reporting/Dashboard/index.html.twig', [
            'data' => $data,
        ]);
    }
}
