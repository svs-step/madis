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

namespace App\Domain\Reporting\Generator\Word;

use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface GeneratorInterface
{
    public const DATE_FORMAT      = 'd/m/Y';
    public const DATE_TIME_FORMAT = 'd/m/Y à H:i';
    public const DATE_TIME_ZONE   = 'Europe/Paris';

    public const TABLE_ORIENTATION_HORIZONTAL = 'horizontal';
    public const TABLE_ORIENTATION_VERTICAL   = 'vertical';

    /**
     * Initialize the Document
     * - Configuration
     * - Default tyle.
     */
    public function initializeDocument(PhpWord $document): void;

    /**
     * Generate the response
     * - This response parse PhpWord to a Word document
     * - Prepare it in a BinaryFileResponse.
     *
     * @param PhpWord $document     The PhpWord document to use
     * @param string  $documentName The document name to use
     *
     * @return BinaryFileResponse The response
     */
    public function generateResponse(PhpWord $document, string $documentName): BinaryFileResponse;
}
