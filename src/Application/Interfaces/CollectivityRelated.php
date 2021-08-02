<?php

namespace App\Application\Interfaces;

use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;

interface CollectivityRelated
{
    public function getCollectivity(): ?Collectivity;

    public function setCollectivity(Collectivity $collectivity): void;

    public function isInUserServices(User $user): bool;
}
