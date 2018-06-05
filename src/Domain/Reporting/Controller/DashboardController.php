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

use App\Domain\Admin\Model\Collectivity;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function indexAction()
    {
        $collectivities = $this->entityManager->getRepository(Collectivity::class)->findAll();
        $users          = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('Reporting/Dashboard/index.html.twig', [
            'nbCollectivity' => \sizeof($collectivities),
            'nbUser'         => \sizeof($users),
        ]);
    }
}
