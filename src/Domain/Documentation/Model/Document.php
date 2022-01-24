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
class Document
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
     * @ORM\Column(type="string", length=255)
     *
     * @var string|null
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     *
     * @var string|null
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string|null
     */
    private $file;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool|null
     */
    private $pinned;

    /**
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="documents")
     *
     * @var array|null
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Domain\User\Model\User", mappedBy="favoriteDocuments")
     *
     * @var array|null
     */
    private $favoritedUsers;

    /**
     * Answer constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Document
    {
        $this->name = $name;
        return $this;
    }

    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function setCategories(?array $categories): Document
    {
        $this->categories = $categories;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): Document
    {
        $this->url = $url;
        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): Document
    {
        $this->file = $file;
        return $this;
    }

    public function getPinned(): ?bool
    {
        return $this->pinned;
    }

    public function setPinned(?bool $pinned): Document
    {
        $this->pinned = $pinned;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getFavoritedUsers(): ?array
    {
        return $this->favoritedUsers;
    }

    /**
     * @param array|null $favoritedUsers
     */
    public function setFavoritedUsers(?array $favoritedUsers): Document
    {
        $this->favoritedUsers = $favoritedUsers;
        return $this;
    }

}
