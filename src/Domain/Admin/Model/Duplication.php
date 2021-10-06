<?php

declare(strict_types=1);

namespace App\Domain\Admin\Model;

use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Admin\Dictionary\DuplicationTypeDictionary;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\User\Model\Collectivity;
use Doctrine\ORM\PersistentCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Duplication implements LoggableSubject
{
    use HistoryTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var Collectivity
     */
    private $sourceCollectivity;

    /**
     * @var string[]
     */
    private $dataIds;

    /**
     * @var object[]
     */
    private $data;

    /**
     * @var Collectivity[]
     */
    private $targetCollectivities;

    /**
     * @var DuplicatedObject[]|PersistentCollection
     */
    private $duplicatedObjects;

    /**
     * DuplicationDTO constructor.
     *
     * @param Collectivity[] $targetCollectivities
     *
     * @throws \Exception
     */
    public function __construct(string $type, Collectivity $sourceCollectivity, array $targetCollectivities = [], array $duplicatedObjects = [])
    {
        if (!\in_array($type, DuplicationTypeDictionary::getDataKeys())) {
            throw new \RuntimeException('Provided type is not an available one. Please check keys in ' . DuplicationTypeDictionary::class . ' class.');
        }
        $this->id                   = Uuid::uuid4();
        $this->type                 = $type;
        $this->sourceCollectivity   = $sourceCollectivity;
        $this->dataIds              = [];
        $this->data                 = [];
        $this->targetCollectivities = $targetCollectivities;
        $this->duplicatedObjects    = $duplicatedObjects;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getSourceCollectivity(): Collectivity
    {
        return $this->sourceCollectivity;
    }

    public function setSourceCollectivity(Collectivity $sourceCollectivity): void
    {
        $this->sourceCollectivity = $sourceCollectivity;
    }

    /**
     * @return string[]
     */
    public function getDataIds(): array
    {
        return $this->dataIds;
    }

    public function addDataId(string $dataId): void
    {
        $this->dataIds[] = $dataId;
    }

    public function removeDataId(string $dataId): void
    {
        $key = \array_search($dataId, $this->dataIds, true);

        if (false === $key) {
            return;
        }

        unset($this->dataIds[$key]);
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function addData($data): void
    {
        $this->data[] = $data;
    }

    /**
     * @param mixed $data
     */
    public function removeData($data): void
    {
        $key = \array_search($data, $this->data, true);

        if (false === $key) {
            return;
        }

        unset($this->data[$key]);
    }

    /**
     * @return Collectivity[]
     */
    public function getTargetCollectivities(): iterable
    {
        return array_unique(array_map(function ($duplicatedObject) {
            return $duplicatedObject->getCollectivity();
        }, is_array($this->duplicatedObjects) ? $this->duplicatedObjects : $this->duplicatedObjects->getValues()), SORT_REGULAR);
    }

    public function getDuplicatedObjects()
    {
        return $this->duplicatedObjects;
    }

    public function getDuplicatedObjectOfCollectivityAndOriginId(Collectivity $collectivity, string $originId): ?DuplicatedObject
    {
        foreach ($this->getDuplicatedObjects() as $duplicatedObject) {
            if ($duplicatedObject->getCollectivity() === $collectivity && $duplicatedObject->getOriginObjectId() === $originId) {
                return $duplicatedObject;
            }
        }

        return null;
    }

    public function addDuplicatedObjet(DuplicatedObject $duplicatedObject)
    {
        $this->duplicatedObjects[] = $duplicatedObject;
    }

    public function removeDuplicatedObject(DuplicatedObject $duplicatedObject)
    {
        $key = \array_search($duplicatedObject, $this->duplicatedObjects, true);

        if (false === $key) {
            return;
        }

        unset($this->duplicatedObjects[$key]);
    }

    public function __toString()
    {
        return 'Duplication ' . $this->sourceCollectivity->__toString();
    }
}
