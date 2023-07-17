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

use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Documentation\Dictionary\DocumentTypeDictionary;
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
    /**
     * @ORM\Id()
     *
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
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    private $size;

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
     * @var bool|null
     */
    private $removeThumb;

    /**
     * @var string|null
     *
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
     *
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
     *
     * @ORM\JoinTable(name="user_favorite_documents",
     *      joinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     *
     * @var Collection|User[]
     */
    private $favoritedUsers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Model\User", cascade={"persist"})
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @var User|null
     */
    private $creator;

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
        if (null !== $this->categories && $this->categories->contains($category)) {
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

    public function addFavoritedUser(User $user): Document
    {
        if (null === $this->favoritedUsers) {
            $this->favoritedUsers = new ArrayCollection();
        }
        if (!$this->favoritedUsers->contains($user)) {
            $this->favoritedUsers->add($user);
        }

        return $this;
    }

    public function removeFavoritedUser(User $user): Document
    {
        if (null !== $this->favoritedUsers && $this->favoritedUsers->contains($user)) {
            $this->favoritedUsers->removeElement($user);
        }

        return $this;
    }

    public function getUploadedFile(): ?UploadedFile
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(?UploadedFile $uploadedFile): Document
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
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

    public function setThumbUploadedFile(?UploadedFile $thumbUploadedFile): Document
    {
        $this->thumbUploadedFile = $thumbUploadedFile;

        return $this;
    }

    public function getThumbUrl(): ?string
    {
        return $this->thumbUrl;
    }

    public function setThumbUrl(?string $thumbUrl): Document
    {
        $this->thumbUrl = $thumbUrl;

        return $this;
    }

    public function getSize(): ?string
    {
        if ($this->size <= 0) {
            return 'Vide';
        }
        $units = ['o', 'Ko', 'Mo', 'Go', 'To', 'Po', 'Eo', 'Zo', 'Yo'];
        $pow   = floor(log((float) $this->size, 1024));
        $size  = number_format($this->size / pow(1024, $pow), 2, ',', ' ') . ' ' . $units[$pow];

        return $size;
    }

    public function setSize(?int $size): Document
    {
        $this->size = $size;

        return $this;
    }

    public function getType(): ?string
    {
        $extension = strtolower(pathinfo($this->getFile(), PATHINFO_EXTENSION));
        if ('pdf' === $extension) {
            return DocumentTypeDictionary::TYPE_PDF;
        }
        if ('mp4' === $extension || 'm4v' === $extension) {
            return DocumentTypeDictionary::TYPE_MP4;
        }
        if ($this->getIsLink()) {
            return DocumentTypeDictionary::TYPE_LINK;
        }
        if ('docx' === $extension) {
            return DocumentTypeDictionary::TYPE_DOCX;
        }
        if ('png' === $extension || 'jpg' === $extension) {
            return DocumentTypeDictionary::TYPE_IMG;
        }

        return null;
    }

    public function getTypeName(): ?string
    {
        $types     = DocumentTypeDictionary::getTypes();
        $extension = strtolower(pathinfo($this->getFile(), PATHINFO_EXTENSION));

        if ('pdf' === $extension) {
            return $types[DocumentTypeDictionary::TYPE_PDF];
        }
        if (in_array($extension, ['mp4', 'mov', 'wmv', 'avi', 'mpg', 'ogv', 'ogg', 'webm'])) {
            return $types[DocumentTypeDictionary::TYPE_MP4];
        }
        if ($this->getIsLink()) {
            return $types[DocumentTypeDictionary::TYPE_LINK];
        }
        if (in_array($extension, ['docx', 'doc', 'odt'])) {
            return $types[DocumentTypeDictionary::TYPE_DOCX];
        }
        if (in_array($extension, ['png', 'jpg'])) {
            return $types[DocumentTypeDictionary::TYPE_IMG];
        }
        if (in_array($extension, ['mp3', 'm4a', 'ogg', 'wav'])) {
            return $types[DocumentTypeDictionary::TYPE_AUDIO];
        }
        if (in_array($extension, ['ppt', 'pptx', 'odp'])) {
            return $types[DocumentTypeDictionary::TYPE_PPT];
        }
        if (in_array($extension, ['xls', 'xlsx', 'xlsm', 'ods'])) {
            return $types[DocumentTypeDictionary::TYPE_EXCEL];
        }

        return null;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): void
    {
        $this->creator = $creator;
    }

    public function getRemoveThumb(): ?bool
    {
        return $this->removeThumb;
    }

    public function setRemoveThumb(?bool $removeThumb): void
    {
        $this->removeThumb = $removeThumb;
    }
}
