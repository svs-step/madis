<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use App\Domain\AIPD\Dictionary\ReponseAvisDictionary;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AnalyseAvis
{
    private UuidInterface $id;

    /**
     * @see ReponseAvisDictionary
     */
    private string $reponse = ReponseAvisDictionary::REPONSE_NONE;

    private ?\DateTime $date;

    private ?string $detail;

    private AnalyseImpact $analyseImpactReferent;
    private AnalyseImpact $analyseImpactDpd;
    private AnalyseImpact $analyseImpactRepresentant;
    private AnalyseImpact $analyseImpactResponsable;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getReponse(): string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): void
    {
        $this->reponse = $reponse;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): void
    {
        $this->detail = $detail;
    }

    public function getAnalyseImpactReferent(): AnalyseImpact
    {
        return $this->analyseImpactReferent;
    }

    public function setAnalyseImpactReferent(AnalyseImpact $analyseImpactReferent): void
    {
        $this->analyseImpactReferent = $analyseImpactReferent;
    }

    public function getAnalyseImpactDpd(): AnalyseImpact
    {
        return $this->analyseImpactDpd;
    }

    public function setAnalyseImpactDpd(AnalyseImpact $analyseImpactDpd): void
    {
        $this->analyseImpactDpd = $analyseImpactDpd;
    }

    public function getAnalyseImpactRepresentant(): AnalyseImpact
    {
        return $this->analyseImpactRepresentant;
    }

    public function setAnalyseImpactRepresentant(AnalyseImpact $analyseImpactRepresentant): void
    {
        $this->analyseImpactRepresentant = $analyseImpactRepresentant;
    }

    public function getAnalyseImpactResponsable(): AnalyseImpact
    {
        return $this->analyseImpactResponsable;
    }

    public function setAnalyseImpactResponsable(AnalyseImpact $analyseImpactResponsable): void
    {
        $this->analyseImpactResponsable = $analyseImpactResponsable;
    }
}
