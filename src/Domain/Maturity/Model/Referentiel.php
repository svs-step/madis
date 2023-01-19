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

use App\Application\Traits\Model\HistoryTrait;
use App\Domain\User\Model\Collectivity;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Referentiel
{
    use HistoryTrait;

    /**
     * @Serializer\Exclude
     */
    private ?UuidInterface $id;

    private string $name;
    private string $description;

    /**
     * @var \DateTimeImmutable|null
     *
     * @Serializer\Type("DateTimeImmutable")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable|null
     *
     * @Serializer\Type("DateTimeImmutable")
     */
    private $updatedAt;

    /**
     * @var iterable|ReferentielSection[]
     */
    private $referentielSections;

    /**
     * @see CollectivityTypeDictionary
     *
     * @Serializer\Exclude
     */
    private ?iterable $authorizedCollectivityTypes;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->referentielSections = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (\is_null($this->getName())) {
            return '';
        }

        if (\mb_strlen($this->getName()) > 85) {
            return \mb_substr($this->getName(), 0, 85) . '...';
        }

        return $this->getName();
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

    public function getReferentielSections(): iterable
    {
        return $this->referentielSections;
    }
}
