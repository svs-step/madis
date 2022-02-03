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
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @var UploadedFile|null
     */
    private $uploadedFile;

    /**
     * @var UploadedFile|null
     */
    private $thumbUploadedFile;

    /**
     * @var string|null
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    private $thumbUrl;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool|null
     */
    private $pinned;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool|null
     */
    private $isLink;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="documents")
     * @ORM\JoinTable(name="document_categories",
     *      joinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id")}
     *      )
     *
     * @var Collection|Category[]
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Domain\User\Model\User", inversedBy="favoriteDocuments")
     * @ORM\JoinTable(name="user_favorite_documents",
     *      joinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     *
     * @var Collection|User[]
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

    public function getCategories(): ?Collection
    {
        return $this->categories;
    }

    public function setCategories(?Collection $categories): Document
    {
        $this->categories = $categories;

        return $this;
    }

    public function addCategory(Category $category): Document
    {
        if (null === $this->categories) {
            $this->categories = new ArrayCollection();
        }
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): Document
    {
        if (null !== $this->categories && !$this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

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

    public function getFavoritedUsers(): ?Collection
    {
        return $this->favoritedUsers;
    }

    public function setFavoritedUsers(?array $favoritedUsers): Document
    {
        $this->favoritedUsers = $favoritedUsers;

        return $this;
    }

    public function getUploadedFile(): ?UploadedFile
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(?UploadedFile $uploadedFile): void
    {
        $this->uploadedFile = $uploadedFile;
    }

    public function getIsLink(): ?bool
    {
        return $this->isLink;
    }

    public function setIsLink(?bool $isLink): Document
    {
        $this->isLink = $isLink;

        return $this;
    }

    public function getThumbUploadedFile(): ?UploadedFile
    {
        return $this->thumbUploadedFile;
    }

    public function setThumbUploadedFile(?UploadedFile $thumbUploadedFile): void
    {
        $this->thumbUploadedFile = $thumbUploadedFile;
    }

    public function getThumbUrl(): ?string
    {
        return $this->thumbUrl;
    }

    public function setThumbUrl(?string $thumbUrl): void
    {
        $this->thumbUrl = $thumbUrl;
    }
}
