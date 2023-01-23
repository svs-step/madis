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
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\User\Model\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Action de protection / Plan d'action.
 */
class Tool implements LoggableSubject, CollectivityRelated
{
    use CollectivityTrait;
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
     * FR: Mise à jour
     *
     * @var bool|null
     */
    private $update;

    /**
     * FR: Sauvegarde
     *
     * @var bool|null
     */
    private $backup;

    /**
     * FR: Suppression
     *
     * @var bool|null
     */
    private $deletion;

    /**
     * FR: Zone de commentaire libre
     *
     * @var bool|null
     */
    private $has_comment;

    /**
     * FR: Autres
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
        $this->id                           = Uuid::uuid4();
        $this->proofs                       = [];
        $this->contractors = [];
        $this->mesurements       = [];
        $this->treatments       = [];
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

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
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
    public function getEditor(): ?string
    {
        return $this->editor;
    }

    /**
     * @param string|null $editor
     */
    public function setEditor(?string $editor): void
    {
        $this->editor = $editor;
    }

    /**
     * @return bool|null
     */
    public function getArchival(): ?bool
    {
        return $this->archival;
    }

    /**
     * @param bool|null $archival
     */
    public function setArchival(?bool $archival): void
    {
        $this->archival = $archival;
    }

    /**
     * @return bool|null
     */
    public function getEncrypted(): ?bool
    {
        return $this->encrypted;
    }

    /**
     * @param bool|null $encrypted
     */
    public function setEncrypted(?bool $encrypted): void
    {
        $this->encrypted = $encrypted;
    }

    /**
     * @return bool|null
     */
    public function getAccessControl(): ?bool
    {
        return $this->access_control;
    }

    /**
     * @param bool|null $access_control
     */
    public function setAccessControl(?bool $access_control): void
    {
        $this->access_control = $access_control;
    }

    /**
     * @return bool|null
     */
    public function getUpdate(): ?bool
    {
        return $this->update;
    }

    /**
     * @param bool|null $update
     */
    public function setUpdate(?bool $update): void
    {
        $this->update = $update;
    }

    /**
     * @return bool|null
     */
    public function getBackup(): ?bool
    {
        return $this->backup;
    }

    /**
     * @param bool|null $backup
     */
    public function setBackup(?bool $backup): void
    {
        $this->backup = $backup;
    }

    /**
     * @return bool|null
     */
    public function getDeletion(): ?bool
    {
        return $this->deletion;
    }

    /**
     * @param bool|null $deletion
     */
    public function setDeletion(?bool $deletion): void
    {
        $this->deletion = $deletion;
    }

    /**
     * @return bool|null
     */
    public function getHasComment(): ?bool
    {
        return $this->has_comment;
    }

    /**
     * @param bool|null $has_comment
     */
    public function setHasComment(?bool $has_comment): void
    {
        $this->has_comment = $has_comment;
    }

    /**
     * @return bool|null
     */
    public function getOther(): ?bool
    {
        return $this->other;
    }

    /**
     * @param bool|null $other
     */
    public function setOther(?bool $other): void
    {
        $this->other = $other;
    }

    /**
     * @return iterable|null
     */
    public function getTreatments(): iterable|null
    {
        return $this->treatments;
    }

    /**
     * @param iterable|null $treatments
     */
    public function setTreatments(iterable|null $treatments): void
    {
        $this->treatments = $treatments;
    }

    /**
     * @return iterable|null
     */
    public function getContractors(): iterable|null
    {
        return $this->contractors;
    }

    /**
     * @param iterable|null $contractors
     */
    public function setContractors(iterable|null $contractors): void
    {
        $this->contractors = $contractors;
    }

    /**
     * @return iterable|null
     */
    public function getProofs(): iterable|null
    {
        return $this->proofs;
    }

    /**
     * @param iterable|null $proofs
     */
    public function setProofs(iterable|null $proofs): void
    {
        $this->proofs = $proofs;
    }

    /**
     * @return iterable|null
     */
    public function getMesurements(): iterable|null
    {
        return $this->mesurements;
    }

    /**
     * @param iterable|null $mesurements
     */
    public function setMesurements(iterable|null $mesurements): void
    {
        $this->mesurements = $mesurements;
    }


    public function isInUserServices(User $user): bool
    {
        return true;
    }
}
