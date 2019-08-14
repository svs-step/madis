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
     * @var iterable
     */
    private $treatments;

    /**
     * @var iterable
     */
    private $contractors;

    /**
     * @var iterable
     */
    private $mesurements;

    /**
     * @var iterable
     */
    private $requests;

    /**
     * @var iterable
     */
    private $violations;

    /**
     * Proof constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id          = Uuid::uuid4();
        $this->treatments  = [];
        $this->contractors = [];
        $this->mesurements = [];
        $this->requests    = [];
        $this->violations  = [];
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

    /**
     * @return iterable
     */
    public function getTreatments(): iterable
    {
        return $this->treatments;
    }

    /**
     * @param Treatment $treatment
     */
    public function addTreatment(Treatment $treatment): void
    {
        $this->treatments[] = $treatment;
    }

    /**
     * @param Treatment $treatment
     */
    public function removeTreatment(Treatment $treatment): void
    {
        $key = \array_search($treatment, $this->treatments, true);

        if (false === $key) {
            return;
        }

        unset($this->treatments[$key]);
    }

    /**
     * @return iterable
     */
    public function getContractors(): iterable
    {
        return $this->contractors;
    }

    /**
     * @param Contractor $contractor
     */
    public function addContractor(Contractor $contractor): void
    {
        $this->contractors[] = $contractor;
    }

    /**
     * @param Contractor $contractor
     */
    public function removeContractor(Contractor $contractor): void
    {
        $key = \array_search($contractor, $this->contractors, true);

        if (false === $key) {
            return;
        }

        unset($this->contractors[$key]);
    }

    /**
     * @return iterable
     */
    public function getMesurements(): iterable
    {
        return $this->mesurements;
    }

    /**
     * @param Mesurement $mesurement
     */
    public function addMesurement(Mesurement $mesurement): void
    {
        $this->mesurements[] = $mesurement;
    }

    /**
     * @param Mesurement $mesurement
     */
    public function removeMesurement(Mesurement $mesurement): void
    {
        $key = \array_search($mesurement, $this->mesurements, true);

        if (false === $key) {
            return;
        }

        unset($this->mesurements[$key]);
    }

    /**
     * @return iterable
     */
    public function getRequests(): iterable
    {
        return $this->requests;
    }

    /**
     * @param Request $request
     */
    public function addRequest(Request $request): void
    {
        $this->requests[] = $request;
    }

    /**
     * @param Request $request
     */
    public function removeRequest(Request $request): void
    {
        $key = \array_search($request, $this->requests, true);

        if (false === $key) {
            return;
        }

        unset($this->requests[$key]);
    }

    /**
     * @return iterable
     */
    public function getViolations(): iterable
    {
        return $this->violations;
    }

    /**
     * @param Violation $violation
     */
    public function addViolation(Violation $violation): void
    {
        $this->violations[] = $violation;
    }

    /**
     * @param Violation $violation
     */
    public function removeViolation(Violation $violation): void
    {
        $key = \array_search($violation, $this->violations, true);

        if (false === $key) {
            return;
        }

        unset($this->violations[$key]);
    }
}
