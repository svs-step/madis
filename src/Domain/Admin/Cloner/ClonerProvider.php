<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Domain\Admin\Cloner;

use App\Domain\Admin\Dictionary\DuplicationTypeDictionary;

class ClonerProvider
{
    /**
     * @var ClonerInterface
     */
    private $treatmentCloner;

    /**
     * @var ClonerInterface
     */
    private $contractorCloner;

    /**
     * @var ClonerInterface
     */
    private $mesurementCloner;

    public function __construct(
        ClonerInterface $treatmentCloner,
        ClonerInterface $contractorCloner,
        ClonerInterface $mesurementCloner
    ) {
        $this->treatmentCloner  = $treatmentCloner;
        $this->contractorCloner = $contractorCloner;
        $this->mesurementCloner = $mesurementCloner;
    }

    /**
     * Get cloner object thanks to type to clone.
     *
     * @param string $type
     *
     * @return ClonerInterface
     */
    public function getCloner(string $type): ClonerInterface
    {
        switch ($type) {
            case DuplicationTypeDictionary::KEY_TREATMENT:
                return $this->treatmentCloner;
            case DuplicationTypeDictionary::KEY_CONTRACTOR:
                return $this->contractorCloner;
            case DuplicationTypeDictionary::KEY_MESUREMENT:
                return $this->mesurementCloner;
            default:
                throw new \RuntimeException('The provided type ' . $type . ' is not a known one.');
        }
    }
}
