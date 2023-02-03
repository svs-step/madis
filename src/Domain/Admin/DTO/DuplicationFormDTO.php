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

namespace App\Domain\Admin\DTO;

use App\Domain\User\Model\Collectivity;

/**
 * Class DuplicationFormDTO.
 */
class DuplicationFormDTO
{
    /**
     * FR: Type de données à dupliquer.
     *
     * @var string|null
     */
    private $type;

    /**
     * FR: Structure source, à utiliser comme base pour la duplication.
     *
     * @var Collectivity|null
     */
    private $sourceCollectivity;

    /**
     * FR: Liste des données à dupliquer.
     *
     * @var array
     */
    private $data;

    /**
     * @var string|null
     */
    private $targetOption;

    /**
     * FR: Types des collectivités cible, sur lesquelles dupliquer les données.
     *
     * @var array
     */
    private $targetCollectivityTypes;

    /**
     * FR: Liste des structures cible, sur lesquelles dupliquer les données.
     *
     * @var Collectivity[]
     */
    private $targetCollectivities;

    /**
     * DuplicationFormDTO constructor.
     */
    public function __construct()
    {
        $this->data                    = [];
        $this->targetCollectivityTypes = [];
        $this->targetCollectivities    = [];
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getSourceCollectivity(): ?Collectivity
    {
        return $this->sourceCollectivity;
    }

    public function setSourceCollectivity(?Collectivity $sourceCollectivity): void
    {
        $this->sourceCollectivity = $sourceCollectivity;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getTargetOption(): ?string
    {
        return $this->targetOption;
    }

    public function setTargetOption(?string $targetOption): void
    {
        $this->targetOption = $targetOption;
    }

    public function getTargetCollectivityTypes(): array
    {
        return $this->targetCollectivityTypes;
    }

    public function setTargetCollectivityTypes(array $targetCollectivityTypes): void
    {
        $this->targetCollectivityTypes = $targetCollectivityTypes;
    }

    /**
     * @return Collectivity[]
     */
    public function getTargetCollectivities(): array
    {
        return $this->targetCollectivities;
    }

    /**
     * @param Collectivity[] $targetCollectivities
     */
    public function setTargetCollectivities(array $targetCollectivities): void
    {
        $this->targetCollectivities = $targetCollectivities;
    }
}
