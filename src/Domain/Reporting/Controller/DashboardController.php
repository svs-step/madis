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
use App\Domain\Maturity\Model\Survey;
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Repository\Request
     */
    private $requestRepository;

    /**
     * @var UserProvider
     */
    private $userProvider;

    public function __construct(
        EntityManagerInterface $entityManager,
        Repository\Request $requestRepository,
        UserProvider $userProvider
    ) {
        $this->entityManager     = $entityManager;
        $this->requestRepository = $requestRepository;
        $this->userProvider      = $userProvider;
    }

    public function indexAction()
    {
        $data = [
            'contractor' => [
                'all'     => 0,
                'clauses' => [
                    'yes' => 0,
                    'no'  => 0,
                ],
                'conform' => [
                    'yes' => 0,
                    'no'  => 0,
                ],
            ],
            'maturity'   => [],
            'mesurement' => [
                'value' => [
                    'applied'       => 0,
                    'notApplied'    => 0,
                    'notApplicable' => 0,
                    'planified'     => 0,
                ],
                'percent' => [
                    'applied'       => 0,
                    'notApplied'    => 0,
                    'notApplicable' => 0,
                    'planified'     => 0,
                ],
            ],
            'request' => [
                'value' => [
                    'all'       => 0,
                    'toProcess' => 0,
                ],
            ],
            'treatment' => [
                'value' => [
                    'active'  => 0,
                    'numeric' => 0,
                    'data'    => [
                        'securityAccessControl' => [
                            'yes' => 0,
                            'no'  => 0,
                        ],
                        'securityTracability' => [
                            'yes' => 0,
                            'no'  => 0,
                        ],
                        'securitySaving' => [
                            'yes' => 0,
                            'no'  => 0,
                        ],
                        'securityUpdate' => [
                            'yes' => 0,
                            'no'  => 0,
                        ],
                        'securityOther' => [
                            'yes' => 0,
                            'no'  => 0,
                        ],
                    ],
                ],
            ],
            'violation' => [
                'value' => [
                    'all' => 0,
                ],
            ],
        ];

        $contractors = $this->entityManager->getRepository(Model\Contractor::class)->findBy(
            ['collectivity' => $this->userProvider->getAuthenticatedUser()->getCollectivity()]
        );
        $maturity = $this->entityManager->getRepository(Survey::class)->findBy(
            ['collectivity' => $this->userProvider->getAuthenticatedUser()->getCollectivity()],
            ['createdAt' => 'DESC'],
            2
        );
        $mesurements = $this->entityManager->getRepository(Model\Mesurement::class)->findBy(
            ['collectivity' => $this->userProvider->getAuthenticatedUser()->getCollectivity()]
        );
        $requests = $this->requestRepository->findAllByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity()
        );
        $treatments = $this->entityManager->getRepository(Model\Treatment::class)->findBy(
            ['collectivity' => $this->userProvider->getAuthenticatedUser()->getCollectivity()]
        );
        $violations = $this->entityManager->getRepository(Model\Violation::class)->findBy(
            ['collectivity' => $this->userProvider->getAuthenticatedUser()->getCollectivity()]
        );

        // =========================
        // PROCESS DATA MANIPULATION
        // =========================

        // CONTRACTOR
        foreach ($contractors as $contractor) {
            ++$data['contractor']['all'];
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

        // MATURITY
        if (isset($maturity[0])) {
            $data['maturity']['new']['name'] = $maturity[0]->getCreatedAt()->format('d/m/Y');
            foreach ($maturity[0]->getMaturity() as $item) {
                $data['maturity']['new']['data'][$item->getDomain()->getName()] = $item->getScore() / 10;
            }
        }
        if (isset($maturity[1])) {
            $data['maturity']['old']['name'] = $maturity[1]->getCreatedAt()->format('d/m/Y');
            foreach ($maturity[1]->getMaturity() as $item) {
                $data['maturity']['old']['data'][$item->getDomain()->getName()] = $item->getScore() / 10;
            }
        }

        // MESUREMENT
        foreach ($mesurements as $mesurement) {
            switch ($mesurement->getStatus()) {
                case MesurementStatusDictionary::STATUS_APPLIED:
                    $data['mesurement']['value']['applied']++;
                    break;
                case MesurementStatusDictionary::STATUS_NOT_APPLIED:
                    $data['mesurement']['value']['notApplied']++;
                    if (!\is_null($mesurement->getPlanificationDate())) {
                        ++$data['mesurement']['value']['planified'];
                    }
                    break;
                case MesurementStatusDictionary::STATUS_NOT_APPLICABLE:
                    $data['mesurement']['value']['notApplicable']++;
            }
        }
        // Only percent if there is non zero values
        if (0 < $data['mesurement']['value']['applied']) {
            $data['mesurement']['percent']['applied'] = $data['mesurement']['value']['applied'] * 100 / (\count($mesurements) - $data['mesurement']['value']['notApplicable']);
        }
        if (0 < $data['mesurement']['value']['notApplied']) {
            $data['mesurement']['percent']['notApplied'] = $data['mesurement']['value']['notApplied'] * 100 / (\count($mesurements) - $data['mesurement']['value']['notApplicable']);
        }
        if (0 < $data['mesurement']['value']['notApplicable']) {
            $data['mesurement']['percent']['notApplicable'] = $data['mesurement']['value']['notApplicable'] * 100 / \count($mesurements);
        }
        if (0 < $data['mesurement']['value']['planified']) {
            $data['mesurement']['percent']['planified'] = $data['mesurement']['value']['planified'] * 100 / (\count($mesurements) - $data['mesurement']['value']['notApplicable']);
        }

        // REQUEST
        $data['request']['value']['all'] = \count($requests);
        foreach ($requests as $request) {
            if ($request->isComplete() && $request->isLegitimateApplicant() && $request->isLegitimateRequest()) {
                ++$data['request']['value']['toProcess'];
            }
        }

        // TREATMENT
        foreach ($treatments as $treatment) {
            if ($treatment->isActive()) {
                ++$data['treatment']['value']['active'];
            }

            // Numeric treatment
            if (!\is_null($treatment->getSoftware())) {
                ++$data['treatment']['value']['numeric'];

                if ($treatment->getSecurityAccessControl()->isCheck()) {
                    ++$data['treatment']['value']['data']['securityAccessControl']['yes'];
                } else {
                    ++$data['treatment']['value']['data']['securityAccessControl']['no'];
                }
                if ($treatment->getSecurityTracability()->isCheck()) {
                    ++$data['treatment']['value']['data']['securityTracability']['yes'];
                } else {
                    ++$data['treatment']['value']['data']['securityTracability']['no'];
                }
                if ($treatment->getSecuritySaving()->isCheck()) {
                    ++$data['treatment']['value']['data']['securitySaving']['yes'];
                } else {
                    ++$data['treatment']['value']['data']['securitySaving']['no'];
                }
                if ($treatment->getSecurityUpdate()->isCheck()) {
                    ++$data['treatment']['value']['data']['securityUpdate']['yes'];
                } else {
                    ++$data['treatment']['value']['data']['securityUpdate']['no'];
                }
                if ($treatment->getSecurityOther()->isCheck()) {
                    ++$data['treatment']['value']['data']['securityOther']['yes'];
                } else {
                    ++$data['treatment']['value']['data']['securityOther']['no'];
                }
            }
        }

        // VIOLATION
        $data['violation']['value']['all'] = \count($violations);

        return $this->render('Reporting/Dashboard/index.html.twig', [
            'data' => $data,
        ]);
    }
}
