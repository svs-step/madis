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
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Notification\Form\Type\NotificationMailParametersType;
use App\Domain\Notification\Model;
use App\Domain\Notification\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationMailParametersController extends CRUDController
{
    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(ControllerHelper $helper, RouterInterface $router) {
        $this->helper = $helper;
        $this->router = $router;
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
        return Model\NotificationMailParameters::class;
    }

    protected function getFormType(): string
    {
        return NotificationMailParametersType::class;
    }

    public function createNotifMailParams()
    {
        $form = $this->helper->createForm(NotificationMailParametersType::class);

        return $this->render('Notification/NotificationMailParameters/create.html.twig',
            [
                'form' => $form->createView(),
            ]);
    }
}
