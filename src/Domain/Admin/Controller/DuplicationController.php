<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Domain\Admin\Controller;

use App\Domain\Admin\Cloner\ClonerProvider;
use App\Domain\Admin\Dictionary\DuplicationTypeDictionary;
use App\Domain\Admin\DTO\DuplicationFormDTO;
use App\Domain\Admin\Form\Type\DuplicationType;
use App\Domain\Admin\Hydrator\DuplicationHydrator;
use App\Domain\Admin\Model\DuplicatedObject;
use App\Domain\Admin\Model\Duplication;
use App\Domain\Admin\Repository as AdminRepository;
use App\Domain\Admin\Transformer\DuplicationFormDTOTransformer;
use App\Domain\User\Repository as UserRepository;
// utilisés dynamiquements pour revert duplication, ne pas supprimer
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DuplicationController extends AbstractController
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var AdminRepository\Duplication
     */
    private $duplicationRepository;

    /**
     * @var UserRepository\Collectivity
     */
    private $collectivityRepository;

    /**
     * @var DuplicationHydrator
     */
    private $duplicationHydrator;

    /**
     * @var DuplicationFormDTOTransformer
     */
    private $dtoTransformer;

    /**
     * @var ClonerProvider
     */
    private $clonerProvider;

    public function __construct(
        RequestStack $requestStack,
        AdminRepository\Duplication $duplicationRepository,
        UserRepository\Collectivity $collectivityRepository,
        DuplicationHydrator $duplicationHydrator,
        DuplicationFormDTOTransformer $dtoTransformer,
        ClonerProvider $clonerProvider
    ) {
        $this->requestStack           = $requestStack;
        $this->duplicationRepository  = $duplicationRepository;
        $this->collectivityRepository = $collectivityRepository;
        $this->duplicationHydrator    = $duplicationHydrator;
        $this->dtoTransformer         = $dtoTransformer;
        $this->clonerProvider         = $clonerProvider;
    }

    /**
     * Show new duplication page form.
     *
     * @throws \Exception
     */
    public function newAction(): Response
    {
        $request = $this->requestStack->getMasterRequest();

        $dto  = new DuplicationFormDTO();
        $form = $this->createForm(
            DuplicationType::class,
            $dto,
            [
                'validation_groups' => [
                    'default',
                ],
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $model = $this->dtoTransformer->toModelObject($dto);
            $this->duplicationRepository->insert($model);
            // An improvement would be to persist DuplicationDTO object in database and only send DuplicationDTO ID

            return $this->redirectToRoute('admin_duplication_processing', [
                'duplicationId' => $model->getId()->toString(),
            ]);
        }

        return $this->render('Admin/Duplication/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Show duplication processing page.
     */
    public function processingAction(string $duplicationId): Response
    {
        $duplication = $this->duplicationRepository->findOneById($duplicationId);
        if (null === $duplication) {
            throw new NotFoundHttpException('No duplication found for ID ' . $duplicationId);
        }

        $this->duplicationHydrator->hydrate($duplication);

        return $this->render('Admin/Duplication/processing.html.twig', [
            'duplication' => $duplication,
        ]);
    }

    /**
     * Action called with AJAX call
     * Make duplication thanks to specified in duplication model.
     */
    public function duplicateAction(string $duplicationId, string $targetCollectivityId): JsonResponse
    {
        // Prefill objects
        $targetCollectivity = $this->collectivityRepository->findOneById($targetCollectivityId);
        if (null === $targetCollectivity) {
            throw new NotFoundHttpException('No collectivity found for ID ' . $targetCollectivityId);
        }
        $duplication = $this->duplicationRepository->findOneById($duplicationId);
        if (null === $duplication) {
            throw new NotFoundHttpException('No duplication found for ID ' . $duplicationId);
        }
        $this->duplicationHydrator->hydrate($duplication);

        // Check that targetCollectivity is a valid Duplication target
        if (!\in_array($targetCollectivity, \iterable_to_array($duplication->getTargetCollectivities()))) {
            throw new BadRequestHttpException('Collectivity with ID ' . $targetCollectivity->getId()->toString() . ' was not found in Duplication ' . $duplication->getId()->toString());
        }

        $this->clonerProvider->getCloner($duplication->getType())->cloneToSpecifiedTarget($duplication, $targetCollectivity);

        return new JsonResponse();
    }

    public function revertAction()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $duplicationId = $entityManager->getRepository(Duplication::class)->findBy([], ['createdAt' => 'DESC'], 1)[0];

        if ($duplicationId) {
            $duplication = $entityManager->getRepository(Duplication::class)->find($duplicationId);

            $objectsToDelete   = $entityManager->getRepository(DuplicatedObject::class)->findBy(['duplication' => $duplication]);
            $objectIdsToDelete = array_map(function ($element) {
                return $element->getDuplicatId();
            }, $objectsToDelete);

            try {
                $typeToDelete = DuplicationTypeDictionary::getClassName($duplication->getType());
            } catch (Exception $e) {
                $this->addFlash('error', 'Impossible d\'annuler la dernière duplication');

                return $this->redirectToRoute('admin_duplication_new');
            }

            // Aller chercher puis supprimer tous les objets liés à la duplication
            if ($objectIdsToDelete) {
                foreach ($objectIdsToDelete as $objectId) {
                    $objectDuplicated = $entityManager
                    ->getRepository(DuplicatedObject::class)
                    ->findOneBy(['duplication' => $duplication, 'duplicatId' => $objectId]);

                    if ($objectDuplicated) {
                        $objectToDelete = $entityManager->getRepository($typeToDelete)->find($objectDuplicated->getDuplicatId());
                        $entityManager->remove($objectToDelete);
                    }
                }
                $entityManager->remove($duplication);

                $entityManager->flush();

                $this->addFlash('success', 'La dernière duplication a été annulée.');
            } else {
                $this->addFlash('error', 'Il n\'y a aucune duplication à annuler');
            }
        } else {
            $this->addFlash('error', 'Il n\'y a aucune duplication à annuler');
        }

        return $this->redirectToRoute('admin_duplication_new');
    }
}
