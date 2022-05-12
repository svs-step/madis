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

namespace App\Domain\Documentation\Model;

use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Category
{
    use HistoryTrait;
    use CreatorTrait;
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     *
     * @var UuidInterface
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool|null
     */
    private $system;

    /**
     * @ORM\ManyToMany(targetEntity="Document", mappedBy="categories")
     * @ORM\JoinTable(name="document_categories",
     *      joinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     *
     * @var array|null
     */
    private $documents;

    /**
     * Category constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function __toString()
    {
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

    public function getSystem(): ?bool
    {
        return $this->system;
    }

    public function setSystem(?bool $system): void
    {
        $this->system = $system;
    }

    public function getDocuments(): ?iterable
    {
        return $this->documents;
    }

    public function setDocuments(?array $documents): void
    {
        $this->documents = $documents;
    }
}
