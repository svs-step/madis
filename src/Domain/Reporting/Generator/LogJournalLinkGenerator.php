<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
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

namespace App\Domain\Reporting\Generator;

use App\Domain\Maturity\Model\Survey;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Proof;
use App\Domain\Reporting\Model\LogJournal;
use App\Domain\User\Model\User;
use Doctrine\Common\Persistence\Proxy;
use Symfony\Component\Routing\RouterInterface;

class LogJournalLinkGenerator
{
    const DELETE_LABEL = 'Supprimé';

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getLink(LogJournal $log)
    {
        if (null === $log->getSubject()) {
            return self::DELETE_LABEL;
        }

        $classname = \get_class($log->getSubject());
        $subject   = $log->getSubject();

        /* Sometimes doctrine retrieve a Proxy object instead of a true object for the subject for an unknown reason
           To avoid any issue we retrieve the classname of the parent if the subject is a proxy */
        if ($subject instanceof Proxy) {
            $reflect   = new \ReflectionClass($subject);
            $parent    = $reflect->getParentClass();
            $classname = $parent->getName();
        }

        switch ($classname) {
            case User::class:
                return $this->router->generate('user_user_edit', ['id' => $log->getSubject()->getId()]);
            case ConformiteTraitement::class:
            case Proof::class:
            case Survey::class:
                return $this->router->generate($log->getSubjectType() . '_edit', ['id' => $log->getSubject()->getId()]);
            case Evaluation::class:
                return $this->router->generate('registry_conformite_organisation_edit', ['id' => $log->getSubject()->getId()]);
            default:
                return $this->router->generate($log->getSubjectType() . '_show', ['id' => $log->getSubject()->getId()]);
        }
    }
}
