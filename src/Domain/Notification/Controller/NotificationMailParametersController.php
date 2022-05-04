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

namespace App\Domain\Notification\Controller;

use App\Application\Controller\ControllerHelper;
use App\Application\Controller\CRUDController;
use App\Domain\Notification\Form\Type\NotificationMailParametersType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;


class NotificationMailParametersController extends CRUDController
{
    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        ControllerHelper $helper,
        RouterInterface $router,
        RequestStack $requestStack
    ) {
        $this->helper = $helper;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->entityManager          = $entityManager;
    }

    protected function getDomain(): string
    {
        return 'notifications';
    }

    protected function getModel(): string
    {
        return 'notificationMailParameters';
    }

    protected function getModelClass(): string
    {
        return \App\Domain\User\Model\Notification::class;
    }

    protected function getFormType(): string
    {
        return NotificationMailParametersType::class;
    }

    public function createNotifMailParams(string $id = null)
    {
        if($id){
            $notifMailParameters = $this->getDoctrine()->getRepository(\App\Domain\User\Model\Notification::class)->find($id);

            if (null === $notifMailParameters) {
                throw new NotFoundHttpException("No object found with ID '{$id}'");
            }
            $form = $this->helper->createForm(NotificationMailParametersType::class, $notifMailParameters);
        } else{
            $form = $this->helper->createForm(NotificationMailParametersType::class);
        }

        $request = $this->requestStack->getMasterRequest();
        $parameters = $request->request->all();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($id)){
                $notifParams = $notifMailParameters;

            } else {
                $mailParams = $parameters['notification_mail_parameters'];
                $notifParams = new \App\Domain\User\Model\Notification();
            }

            if (isset($mailParams['is_notified'])){ $notifParams->setIsNotified($parameters['notification_mail_parameters']['is_notified']);}
            $notifParams->setFrequency($parameters['alert']);

            switch ($parameters['alert']) {
                case 'every_hours':
                    $notifParams->setIntervalHours(intval($parameters['alert']));
                    break;
                case 'daily' :
                    $notifParams->setStartHours(intval($parameters['daily_hour']));
                    break;
                case 'weekly' :
                    $notifParams->setStartHours(intval($parameters['weekly_hour']));
                    $notifParams->setStartDay($parameters['weekly_day']);
                    break;
                case 'monthly' :
                    $notifParams->setStartHours(intval($parameters['monthly_hour']));
                    $notifParams->setStartDay($parameters['monthly_day']);
                    $notifParams->setStartWeek($parameters['monthly_week']);
                    break;
            }

            if (isset($mailParams['is_treatment'])){ $notifParams->setIsTreatment($mailParams['is_treatment']);}
            if (isset($mailParams['is_subcontract'])){$notifParams->setIsSubcontract($mailParams['is_subcontract']);}
            if (isset($mailParams['is_request'])){$notifParams->setIsRequest($mailParams['is_request']);}
            if (isset($mailParams['is_violation'])){$notifParams->setIsViolation($mailParams['is_violation']);}
            if (isset($mailParams['is_proof'])){$notifParams->setIsProof($mailParams['is_proof']);}
            if (isset($mailParams['is_protectAction'])){$notifParams->setIsProtectAction($mailParams['is_protectAction']);}
            if (isset($mailParams['is_maturity'])){$notifParams->setIsMaturity($mailParams['is_maturity']);}
            if (isset($mailParams['is_treatmenConformity'])){$notifParams->setIsTreatmenConformity($mailParams['is_treatmenConformity']);}
            if (isset($mailParams['is_organizationConformity'])){$notifParams->setIsOrganizationConformity($mailParams['is_organizationConformity']);}
            if (isset($mailParams['is_AIPD'])){$notifParams->setIsAIPD($mailParams['is_AIPD']);}
            if (isset($mailParams['is_document'])){$notifParams->setIsDocument($mailParams['is_document']);}

            $this->entityManager->persist($notifParams);
            $this->entityManager->flush();

            //todo une fois que le modele est enregistré, revenir sur le user
        }

        if (!isset($notifParams)){
            return $this->render('Notification/NotificationMailParameters/create.html.twig',
                [
                    'form' => $form->createView()
                ]);
        } else{
            return $this->render('Notification/NotificationMailParameters/create.html.twig',
                [
                    'form' => $form->createView(),
                    'alert' => $notifParams->getFrequency(),
                    'every_hours' => $notifParams->getIntervalHours(),
                    'daily_hour' => $notifParams->getStartHours(),
                    'weekly_hour' => $notifParams->getStartHours(),
                    'weekly_day' => $notifParams->getStartDay(),
                    'monthly_hour' => $notifParams->getStartHours(),
                    'monthly_day' => $notifParams->getStartDay(),
                    'monthly_week' => $notifParams->getStartWeek()
                ]);
        }

    }
}
