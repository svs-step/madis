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

namespace App\Domain\Documentation\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Documentation\Form\Type\CategoryType;
use App\Domain\Documentation\Model;
use App\Domain\Documentation\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Category $repository
 */
class CategoryController extends CRUDController
{
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
        Repository\Category $repository,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDomain(): string
    {
        return 'documentation';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel(): string
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Category::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return CategoryType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        $order = [
            'createdAt' => 'DESC',
        ];

        // Everybody can access all documents
        return $this->repository->findAll($order);
    }

    /**
     * {@inheritdoc}
     * Here, we wanna compute maturity score.
     *
     * @param Model\Survey $object
     */
    public function formPrePersistData($object)
    {
        $object->setSystem(false);
    }

    /**
     * Generate the flashbag message dynamically depending on the domain, model & object.
     * Replace word `%object%` in translation by the related object (thanks to it `__toString` method).
     *
     * @param string      $type     The flashbag type
     * @param string|null $template The related template to use
     * @param mixed|null  $object   The object to use to generate flashbag (eg. show object name)
     *
     * @return string The generated flashbag
     */
    protected function getFlashbagMessage(string $type, string $template = null, $object = null): string
    {
        $params = [];

        return $this->translator->trans(
            "{$this->getDomain()}.{$this->getModel()}.flashbag.{$type}.{$template}",
            $params
        );
    }
}
