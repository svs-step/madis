<?php

namespace App\Domain\User\Model;

use App\Application\Traits\Model\HistoryTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class EmailNotificationPreference
{
    use HistoryTrait;

    public const NOTIF_TREATMENT               = 1;
    public const NOTIF_SUBCONTRACTOR           = 2;
    public const NOTIF_REQUEST                 = 4;
    public const NOTIF_VIOLATION               = 8;
    public const NOTIF_PROOF                   = 16;
    public const NOTIF_PROTECT_ACTION          = 32;
    public const NOTIF_MATURITY                = 64;
    public const NOTIF_TREATMENT_CONFORMITY    = 128;
    public const NOTIF_ORGANIZATION_CONFORMITY = 256;
    public const NOTIF_AIPD                    = 512;
    public const NOTIF_DOCUMENT                = 1024;

    public const FREQUENCY_NONE  = 'none';
    public const FREQUENCY_EACH  = 'each';
    public const FREQUENCY_HOUR  = 'hour';
    public const FREQUENCY_DAY   = 'day';
    public const FREQUENCY_WEEK  = 'week';
    public const FREQUENCY_MONTH = 'month';

    public const MODULES = [
        'treatment'               => self::NOTIF_TREATMENT,
        'subcontractor'           => self::NOTIF_SUBCONTRACTOR,
        'request'                 => self::NOTIF_REQUEST,
        'violation'               => self::NOTIF_VIOLATION,
        'proof'                   => self::NOTIF_PROOF,
        'protect_action'          => self::NOTIF_PROTECT_ACTION,
        'maturity'                => self::NOTIF_MATURITY,
        'treatment_conformity'    => self::NOTIF_TREATMENT_CONFORMITY,
        'organization_conformity' => self::NOTIF_ORGANIZATION_CONFORMITY,
        'aipd'                    => self::NOTIF_AIPD,
        'document'                => self::NOTIF_DOCUMENT,
    ];

    /**
     * @var UuidInterface
     */
    private $id;

    private User $user;

    private string $frequency;

    private bool $enabled;

    private ?int $hour;

    private ?int $week;

    private ?int $day;

    private int $notificationMask;

    private ?\DateTime $lastSent;

    public function __construct()
    {
        $this->id               = Uuid::uuid4();
        $this->notificationMask = 2047;     // All active by default. https://gitlab.adullact.net/soluris/madis/-/issues/632
        $this->enabled          = 1;
        $this->frequency        = 'none';
        $this->lastSent         = new \DateTime();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(?string $frequency): void
    {
        $this->frequency = $frequency;
    }

    public function getHour(): ?int
    {
        return $this->hour;
    }

    public function setHour(?int $hour): void
    {
        $this->hour = $hour;
    }

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function setWeek(?int $week): void
    {
        $this->week = $week;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): void
    {
        $this->day = $day;
    }

    public function getLastSent(): ?\DateTime
    {
        return $this->lastSent;
    }

    public function setLastSent(?\DateTime $lastSent
    ): void {
        $this->lastSent = $lastSent;
    }

    public function getNotificationMask(): int
    {
        return $this->notificationMask;
    }

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
