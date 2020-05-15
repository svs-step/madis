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

namespace App\Domain\Registry\Model\ConformiteTraitement;

use App\Domain\Registry\Model\Mesurement;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Reponse
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var bool
     */
    private $conforme;

    /**
     * @var Question
     */
    private $question;

    /**
     * @var ConformiteTraitement
     */
    private $conformiteTraitement;

    /**
     * @var iterable
     */
    private $actionProtections;

    public function __construct()
    {
        $this->id                = Uuid::uuid4();
        $this->conforme          = false;
        $this->actionProtections = [];
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function isConforme(): bool
    {
        return $this->conforme;
    }

    public function setConforme(bool $conforme): void
    {
        $this->conforme = $conforme;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    public function getConformiteTraitement(): ConformiteTraitement
    {
        return $this->conformiteTraitement;
    }

    public function setConformiteTraitement(ConformiteTraitement $conformiteTraitement): void
    {
        $this->conformiteTraitement = $conformiteTraitement;
    }

    public function getActionProtections(): iterable
    {
        return $this->actionProtections;
    }

    public function addActionProtection(Mesurement $mesurement): void
    {
        $this->actionProtections[] = $mesurement;
    }

    public function removeActionProtection(Mesurement $mesurement): void
    {
        $key = \array_search($mesurement, $this->actionProtections, true);

        if (false === $key) {
            return;
        }

        unset($this->actionProtections[$key]);
    }
}
