<?php

declare(strict_types=1);

namespace App\Domain\Admin\Model;

use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Admin\Dictionary\DuplicationTypeDictionary;
use App\Domain\User\Model\Collectivity;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Duplication
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
     * DuplicationDTO constructor.
     *
     * @param string         $type
     * @param Collectivity   $sourceCollectivity
     * @param Collectivity[] $targetCollectivities
     *
     * @throws \Exception
     */
    public function __construct(string $type, Collectivity $sourceCollectivity, array $targetCollectivities = [])
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
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Collectivity
     */
    public function getSourceCollectivity(): Collectivity
    {
        return $this->sourceCollectivity;
    }

    /**
     * @param Collectivity $sourceCollectivity
     */
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

    /**
     * @param string $dataId
     */
    public function addDataId(string $dataId): void
    {
        $this->dataIds[] = $dataId;
    }

    /**
     * @param string $dataId
     */
    public function removeDataId(string $dataId): void
    {
        $key = \array_search($dataId, $this->dataIds, true);

        if (false === $key) {
            return;
        }

        unset($this->dataIds[$key]);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param $data
     */
    public function addData($data): void
    {
        $this->data[] = $data;
    }

    /**
     * @param $data
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
        return $this->targetCollectivities;
    }

    /**
     * @param Collectivity $collectivity
     */
    public function addTargetCollectivity(Collectivity $collectivity): void
    {
        $this->targetCollectivities[] = $collectivity;
    }

    /**
     * @param Collectivity $collectivity
     */
    public function removeTargetCollectivity(Collectivity $collectivity): void
    {
        $key = \array_search($collectivity, $this->targetCollectivities, true);

        if (false === $key) {
            return;
        }

        unset($this->targetCollectivities[$key]);
    }
}
