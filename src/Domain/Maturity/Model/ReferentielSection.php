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

namespace App\Domain\Maturity\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ReferentielSection
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var iterable|ReferentielQuestion[]
     */
    private $referentielQuestions;

    /**
     * @var Referentiel|null
     */
    private $referentiel;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->referentielQuestions = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getReferentielQuestions(): iterable
    {
        return $this->referentielQuestions;
    }

    public function setReferentielQuestions(?iterable $referentielQuestions): void
    {
        $this->referentielQuestions = $referentielQuestions;
    }

    public function addReferentielQuestion(ReferentielQuestion $referentielQuestion): void
    {
        $this->referentielQuestions[] = $referentielQuestion;
    }

    public function removeReferentielQuestion(ReferentielQuestion $referentielQuestion): void
    {
        $key = \array_search($referentielQuestion, $this->referentielQuestions, true);

        if (false === $key) {
            return;
        }

        unset($this->referentielQuestions[$key]);
    }

    public function getReferentiel(): ?Referentiel
    {
        return $this->referentiel;
    }

    public function setReferentiel(?Referentiel $referentiel): void
    {
        $this->referentiel = $referentiel;
    }


}
