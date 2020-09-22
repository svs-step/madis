<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
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

namespace App\Domain\Reporting\Handler;

use App\Domain\Reporting\Metrics\AdminMetric;
use App\Domain\Reporting\Metrics\MetricInterface;
use App\Domain\Reporting\Metrics\UserMetric;
use App\Domain\User\Dictionary\UserRoleDictionary;
use Symfony\Component\Security\Core\Security;

class MetricsHandler
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var UserMetric
     */
    private $userMetric;

    /**
     * @var AdminMetric
     */
    private $adminMetric;

    public function __construct(
        Security $security,
        UserMetric $userMetric,
        AdminMetric $adminMetric
    ) {
        $this->security    = $security;
        $this->userMetric  = $userMetric;
        $this->adminMetric = $adminMetric;
    }

    public function getHandler(): MetricInterface
    {
        $user = $this->security->getUser();
        $role = $user->getRoles()[0];

        switch ($role) {
            case UserRoleDictionary::ROLE_ADMIN:
            case UserRoleDictionary::ROLE_REFERENT:
                return $this->adminMetric;
                break;
            default:
                return $this->userMetric;
        }
    }
}
