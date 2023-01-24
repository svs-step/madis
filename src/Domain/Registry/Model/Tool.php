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

namespace App\Domain\Registry\Model;

use App\Application\Interfaces\CollectivityRelated;
use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\User\Model\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Action de protection / Plan d'action.
 */
class Tool implements LoggableSubject
{
    use CreatorTrait;
    use HistoryTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * FR: Nom.
     *
     * @var string|null
     */
    private $name;

    /**
     * FR: Type.
     *
     * @var string|null
     */
    private $type;

    /**
     * FR: Editeur.
     *
     * @var string|null
     */
    private $editor;

    /**
     * FR: Archivage.
     *
     * @var bool|null
     */
    private $archival;

    /**
     * FR: Chiffrement.
     *
     * @var bool|null
     */
    private $encrypted;

    /**
     * FR: Controle d'accès.
     *
     * @var bool|null
     */
    private $access_control;

    /**
     * FR: Mise à jour.
     *
     * @var bool|null
     */
    private $update;

    /**
     * FR: Sauvegarde.
     *
     * @var bool|null
     */
    private $backup;

    /**
     * FR: Suppression.
     *
     * @var bool|null
     */
    private $deletion;

    /**
     * FR: Zone de commentaire libre.
     *
     * @var bool|null
     */
    private $has_comment;

    /**
     * FR: Autres.
     *
     * @var bool|null
     */
    private $other;

    private ?iterable $treatments;
    private ?iterable $contractors;
    private ?iterable $proofs;
    private ?iterable $mesurements;

    /**
     * Mesurement constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id          = Uuid::uuid4();
        $this->proofs      = [];
        $this->contractors = [];
        $this->mesurements = [];
        $this->treatments  = [];
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getEditor(): ?string
    {
        return $this->editor;
    }

    public function setEditor(?string $editor): void
    {
        $this->editor = $editor;
    }

    public function getArchival(): ?bool
    {
        return $this->archival;
    }

    public function setArchival(?bool $archival): void
    {
        $this->archival = $archival;
    }

    public function getEncrypted(): ?bool
    {
        return $this->encrypted;
    }

    public function setEncrypted(?bool $encrypted): void
    {
        $this->encrypted = $encrypted;
    }

    public function getAccessControl(): ?bool
    {
        return $this->access_control;
    }

    public function setAccessControl(?bool $access_control): void
    {
        $this->access_control = $access_control;
    }

    public function getUpdate(): ?bool
    {
        return $this->update;
    }

    public function setUpdate(?bool $update): void
    {
        $this->update = $update;
    }

    public function getBackup(): ?bool
    {
        return $this->backup;
    }

    public function setBackup(?bool $backup): void
    {
        $this->backup = $backup;
    }

    public function getDeletion(): ?bool
    {
        return $this->deletion;
    }

    public function setDeletion(?bool $deletion): void
    {
        $this->deletion = $deletion;
    }

    public function getHasComment(): ?bool
    {
        return $this->has_comment;
    }

    public function setHasComment(?bool $has_comment): void
    {
        $this->has_comment = $has_comment;
    }

    public function getOther(): ?bool
    {
        return $this->other;
    }

    public function setOther(?bool $other): void
    {
        $this->other = $other;
    }

    public function getTreatments(): iterable|null
    {
        return $this->treatments;
    }

    public function setTreatments(iterable|null $treatments): void
    {
        $this->treatments = $treatments;
    }

    public function getContractors(): iterable|null
    {
        return $this->contractors;
    }

    public function setContractors(iterable|null $contractors): void
    {
        $this->contractors = $contractors;
    }

    public function getProofs(): iterable|null
    {
        return $this->proofs;
    }

    public function setProofs(iterable|null $proofs): void
    {
        $this->proofs = $proofs;
    }

    public function getMesurements(): iterable|null
    {
        return $this->mesurements;
    }

    public function setMesurements(iterable|null $mesurements): void
    {
        $this->mesurements = $mesurements;
    }

    public function isInUserServices(User $user): bool
    {
        return true;
    }
}
