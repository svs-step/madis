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

use App\Domain\Registry\Model\Treatment;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ConformiteTraitement
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Treatment
     */
    private $traitement;

    /**
     * @var iterable|Reponse[]
     */
    private $reponses;

    /**
     * @var int
     */
    private $nbConformes;

    /**
     * @var int
     */
    private $nbNonConformesMineures;

    /**
     * @var int
     */
    private $nbNonConformesMajeures;

    public function __construct()
    {
        $this->id                       = Uuid::uuid4();
        $this->reponses                 = [];
        $this->nbConformes              = 0;
        $this->nbNonConformesMineures   = 0;
        $this->nbNonConformesMajeures   = 0;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTraitement(): Treatment
    {
        return $this->traitement;
    }

    public function setTraitement(Treatment $traitement): void
    {
        $this->traitement = $traitement;
    }

    /**
     * @return iterable|Reponse[]
     */
    public function getReponses(): iterable
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): void
    {
        $this->reponses[] = $reponse;
        $reponse->setConformiteTraitement($this);
    }

    public function removeReponse(Reponse $reponse): void
    {
        $key = \array_search($reponse, $this->reponses, true);

        if (false === $key) {
            return;
        }

        unset($this->reponses[$key]);
    }

    public function getNbConformes(): int
    {
        return $this->nbConformes;
    }

    public function setNbConformes(int $nbConformes): void
    {
        $this->nbConformes = $nbConformes;
    }

    public function getNbNonConformesMineures(): int
    {
        return $this->nbNonConformesMineures;
    }

    public function setNbNonConformesMineures(int $nbNonConformesMineures): void
    {
        $this->nbNonConformesMineures = $nbNonConformesMineures;
    }

    public function getNbNonConformesMajeures(): int
    {
        return $this->nbNonConformesMajeures;
    }

    public function setNbNonConformesMajeures(int $nbNonConformesMajeures): void
    {
        $this->nbNonConformesMajeures = $nbNonConformesMajeures;
    }

    public function __toString()
    {
        return $this->traitement->__toString();
    }
}
