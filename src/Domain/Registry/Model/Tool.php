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
use App\Domain\Registry\Model\Embeddable\ComplexChoice;
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

    const COUNTRY_FRANCE = 'registry.tool.country.france';
    const COUNTRY_EU = 'registry.tool.country.eu';
    const COUNTRY_OTHER = 'registry.tool.country.other';

    const COUNTRY_FRANCE_TEXT = 'France';
    const COUNTRY_EU_TEXT = 'Autre pays de l’Union Européenne ou pays adéquats';
    const COUNTRY_OTHER_TEXT = 'Pays d’hébergement de la donnée';

    const COUNTRY_TYPES = [
        self::COUNTRY_FRANCE => self::COUNTRY_FRANCE_TEXT,
        self::COUNTRY_EU => self::COUNTRY_EU_TEXT,
        self::COUNTRY_OTHER => self::COUNTRY_OTHER_TEXT,
    ];

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
     * FR: Personne en charge.
     *
     * @var string|null
     */
    private $manager;

    /**
     * FR: Description.
     *
     * @var string|null
     */
    private $description;

    /**
     * FR: Date de mise en prodution.
     *
     * @var \DateTime|null
     */
    private $prod_date;

    /**
     * FR: Pays d'hébergement ou de stockage
     *
     * @var string|null
     */
    private $country_type;

    /**
     * FR: Pays d'hébergement ou de stockage
     *
     * @var string|null
     */
    private $country_name;

    /**
     * FR: Garanties pour le transfert
     *
     * @var string|null
     */
    private $country_guarantees;

    /**
     * FR: Autres informations
     *
     * @var string|null
     */
    private $other_info;

    /**
     * FR: Archivage.
     *
     * @var ComplexChoice
     */
    private $archival;

    /**
     * FR: Traçabilité.
     *
     * @var ComplexChoice
     */
    private $tracking;

    /**
     * FR: Chiffrement.
     *
     * @var ComplexChoice
     */
    private $encrypted;

    /**
     * FR: Controle d'accès.
     *
     * @var ComplexChoice
     */
    private $access_control;

    /**
     * FR: Mise à jour.
     *
     * @var ComplexChoice
     */
    private $update;

    /**
     * FR: Sauvegarde.
     *
     * @var ComplexChoice
     */
    private $backup;

    /**
     * FR: Suppression.
     *
     * @var ComplexChoice
     */
    private $deletion;

    /**
     * FR: Zone de commentaire libre.
     *
     * @var ComplexChoice
     */
    private $has_comment;

    /**
     * FR: Autres.
     *
     * @var ComplexChoice
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
        $this->tracking = new ComplexChoice();
        $this->update = new ComplexChoice();
        $this->archival = new ComplexChoice();
        $this->other = new ComplexChoice();
        $this->has_comment = new ComplexChoice();
        $this->deletion = new ComplexChoice();
        $this->backup = new ComplexChoice();
        $this->update = new ComplexChoice();
        $this->access_control = new ComplexChoice();
        $this->encrypted = new ComplexChoice();
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

    public function getArchival(): ?ComplexChoice
    {
        return $this->archival;
    }

    public function setArchival(?ComplexChoice $archival): void
    {
        $this->archival = $archival;
    }

    public function getEncrypted(): ?ComplexChoice
    {
        return $this->encrypted;
    }

    public function setEncrypted(?ComplexChoice $encrypted): void
    {
        $this->encrypted = $encrypted;
    }

    public function getAccessControl(): ?ComplexChoice
    {
        return $this->access_control;
    }

    public function setAccessControl(?ComplexChoice $access_control): void
    {
        $this->access_control = $access_control;
    }

    public function getUpdate(): ?ComplexChoice
    {
        return $this->update;
    }

    public function setUpdate(?ComplexChoice $update): void
    {
        $this->update = $update;
    }

    public function getBackup(): ?ComplexChoice
    {
        return $this->backup;
    }

    public function setBackup(?ComplexChoice $backup): void
    {
        $this->backup = $backup;
    }

    public function getDeletion(): ?ComplexChoice
    {
        return $this->deletion;
    }

    public function setDeletion(?ComplexChoice $deletion): void
    {
        $this->deletion = $deletion;
    }

    public function getHasComment(): ?ComplexChoice
    {
        return $this->has_comment;
    }

    public function setHasComment(?ComplexChoice $has_comment): void
    {
        $this->has_comment = $has_comment;
    }

    public function getOther(): ?ComplexChoice
    {
        return $this->other;
    }

    public function setOther(?ComplexChoice $other): void
    {
        $this->other = $other;
    }

    public function getTracking(): ?ComplexChoice
    {
        return $this->tracking;
    }

    public function setTracking(?ComplexChoice $tracking): void
    {
        $this->tracking = $tracking;
    }

    /**
     * @return string|null
     */
    public function getManager(): ?string
    {
        return $this->manager;
    }

    /**
     * @param string|null $manager
     */
    public function setManager(?string $manager): void
    {
        $this->manager = $manager;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime|null
     */
    public function getProdDate(): ?\DateTime
    {
        return $this->prod_date;
    }

    /**
     * @param \DateTime|null $prod_date
     */
    public function setProdDate(?\DateTime $prod_date): void
    {
        $this->prod_date = $prod_date;
    }

    /**
     * @return string|null
     */
    public function getCountryType(): ?string
    {
        return $this->country_type;
    }

    /**
     * @param string|null $country_type
     */
    public function setCountryType(?string $country_type): void
    {
        $this->country_type = $country_type;
    }

    /**
     * @return string|null
     */
    public function getCountryName(): ?string
    {
        return $this->country_name;
    }

    /**
     * @param string|null $country_name
     */
    public function setCountryName(?string $country_name): void
    {
        $this->country_name = $country_name;
    }

    /**
     * @return string|null
     */
    public function getCountryGuarantees(): ?string
    {
        return $this->country_guarantees;
    }

    /**
     * @param string|null $country_guarantees
     */
    public function setCountryGuarantees(?string $country_guarantees): void
    {
        $this->country_guarantees = $country_guarantees;
    }

    /**
     * @return string|null
     */
    public function getOtherInfo(): ?string
    {
        return $this->other_info;
    }

    /**
     * @param string|null $other_info
     */
    public function setOtherInfo(?string $other_info): void
    {
        $this->other_info = $other_info;
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
