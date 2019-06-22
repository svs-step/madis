<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Model;

use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Application\Traits\Model\SoftDeletableTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;

class Proof
{
    use CollectivityTrait;
    use CreatorTrait;
    use HistoryTrait;
    use SoftDeletableTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var string|null
     */
    private $document;

    /**
     * @var File|null
     */
    private $documentFile;

    /**
     * @var string|null
     */
    private $comment;

    /**
     * Proof constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (\is_null($this->getName())) {
            return '';
        }

        if (\strlen($this->getName()) > 50) {
            return \substr($this->getName(), 0, 50) . '...';
        }

        return $this->getName();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getDocument(): ?string
    {
        return $this->document;
    }

    /**
     * @param string|null $document
     */
    public function setDocument(?string $document): void
    {
        $this->document = $document;
    }

    /**
     * @return File|null
     */
    public function getDocumentFile(): ?File
    {
        return $this->documentFile;
    }

    /**
     * @param File|null $documentFile
     */
    public function setDocumentFile(?File $documentFile): void
    {
        $this->documentFile = $documentFile;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }
}
