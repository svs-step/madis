<?php

namespace App\Domain\User\Model;

use App\Application\Traits\Model\HistoryTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\User\Model\User;


class EmailNotificationPreference
{
    use HistoryTrait;

    const NOTIF_TREATMENT = 1;
    const NOTIF_SUBCONTRACTOR = 2;
    const NOTIF_REQUEST = 4;
    const NOTIF_VIOLATION = 8;
    const NOTIF_PROOF = 16;
    const NOTIF_PROTECT_ACTION = 32;
    const NOTIF_MATURITY = 64;
    const NOTIF_TREATMENT_CONFORMITY = 128;
    const NOTIF_ORGANIZATION_CONFORMITY = 256;
    const NOTIF_AIPD = 512;
    const NOTIF_DOCUMENT = 1024;
    /**
     * @var UuidInterface
     */
    private $id;

    private User $user;

    private ?string $frequency;

    private bool $enabled;

    private ?int $interval_hours;

    private ?int $start_week;

    private ?int $start_day;

    private ?int $start_hour;

    private int $notificationMask;

    private $last_sent;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->notificationMask = 0;
        $this->enabled = 1;
        $this->interval_hours = 1;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string|null
     */
    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    /**
     * @param string|null $frequency
     */
    public function setFrequency(?string $frequency): void
    {
        $this->frequency = $frequency;
    }

    /**
     * @return int|null
     */
    public function getIntervalHours(): ?int
    {
        return $this->interval_hours;
    }

    /**
     * @param int|null $interval_hours
     */
    public function setIntervalHours(?int $interval_hours): void
    {
        $this->interval_hours = $interval_hours;
    }

    /**
     * @return string|null
     */
    public function getStartWeek(): ?string
    {
        return $this->start_week;
    }

    /**
     * @param string|null $start_week
     */
    public function setStartWeek(?string $start_week): void
    {
        $this->start_week = $start_week;
    }

    /**
     * @return string|null
     */
    public function getStartDay(): ?string
    {
        return $this->start_day;
    }

    /**
     * @param string|null $start_day
     */
    public function setStartDay(?string $start_day): void
    {
        $this->start_day = $start_day;
    }

    /**
     * @return int|null
     */
    public function getStartHour(): ?int
    {
        return $this->start_hour;
    }

    /**
     * @param int|null $start_hour
     */
    public function setStartHour(?int $start_hour): void
    {
        $this->start_hour = $start_hour;
    }

    /**
     * @return mixed
     */
    public function getLastSent()
    {
        return $this->last_sent;
    }

    /**
     * @param mixed $last_sent
     */
    public function setLastSent($last_sent): void
    {
        $this->last_sent = $last_sent;
    }

    /**
     * @return int
     */
    public function getNotificationMask(): int
    {
        return $this->notificationMask;
    }

    /**
     * @param int $notificationMask
     */
    public function setNotificationMask(int $notificationMask): void
    {
        $this->notificationMask = $notificationMask;
    }

    /**
     * @return bool|int
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool|int $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }
}
