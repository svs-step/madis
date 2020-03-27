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

namespace App\Domain\Reporting\Generator\Csv;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface GeneratorInterface
{
    const DATE_FORMAT      = 'd/m/Y';
    const DATE_TIME_FORMAT = 'd/m/Y à H:i';
    const DATE_TIME_ZONE   = 'Europe/Paris';

    /**
     * Initialize the csv extract.
     */
    public function initializeExtract(): array;

    /**
     * Generate the response
     * - This response parse data to a csv
     * - Prepare it in a BinaryFileResponse.
     *
     * @return BinaryFileResponse The response
     */
    public function generateResponse(string $documentName): BinaryFileResponse;
}
