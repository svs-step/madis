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

use App\Application\Traits\Model\HistoryTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Public Configuration / Choose what will be public or not.
 */
class PublicConfiguration
{
    use HistoryTrait;

    public const bannedProperties = [
        Treatment::class => [
            'id',
            'public',
            'createdAt',
            'updatedAt',
            'clonedFrom',
            'templateIdentifier',
            'template',
            'completion',
            'conformiteTraitement',
        ],
    ];

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $savedConfiguration;

    /**
     * @var \stdClass
     */
    private $mappedObject;

    /**
     * @var string
     */
    private $type;

    /**
     * PublicConfiguration constructor.
     *
     * @throws \Exception
     */
    public function __construct(string $type)
    {
        $this->id                 = Uuid::uuid4();
        $this->savedConfiguration = '';
        $this->type               = $type;
        $this->_initMappedObject();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getSavedConfiguration(): ?string
    {
        if (!$this->savedConfiguration) {
            $this->savedConfiguration = json_encode($this->mappedObject);
        }

        return $this->savedConfiguration;
    }

    public function setSavedConfiguration(?string $savedConfiguration)
    {
        $this->savedConfiguration = $savedConfiguration;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMappedObject(): \stdClass
    {
        if (!$this->mappedObject) {
            $this->_initMappedObject();
        }

        return $this->mappedObject;
    }

    public function __toString(): string
    {
        return $this->getSavedConfiguration();
    }

    public function __get(string $name)
    {
        if (!$this->mappedObject) {
            $this->_initMappedObject();
        }

        return $this->mappedObject->$name;
    }

    public function __set(string $name, $value)
    {
        if (!$this->mappedObject) {
            $this->_initMappedObject();
        }
        $this->mappedObject->$name = $value;
        $this->savedConfiguration  = json_encode($this->mappedObject);
    }

    public function __call(string $name, $arguments)
    {
        return $this->__get($name);
    }

    private function _initMappedObject()
    {
        if (!$this->mappedObject) {
            $this->mappedObject = new \stdClass();
            $className          = $this->type;
            $entity             = new $className();
            $reflection         = new \ReflectionClass($entity);
            $properties         = $reflection->getProperties();

            foreach ($properties as $property) {
                $propertyName = $property->name;

                if (!in_array($propertyName, self::bannedProperties[$this->type])) {
                    $this->mappedObject->$propertyName = false;
                }
            }
        }
    }
}
