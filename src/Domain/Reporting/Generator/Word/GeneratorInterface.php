<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Reporting\Generator\Word;

use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface GeneratorInterface
{
    const DATE_FORMAT      = 'd/m/Y';
    const DATE_TIME_FORMAT = 'd/m/Y à H:i';
    const DATE_TIME_ZONE   = 'Europe/Paris';

    const TABLE_ORIENTATION_HORIZONTAL = 'horizontal';
    const TABLE_ORIENTATION_VERTICAL   = 'vertical';

    /**
     * Initialize the Document
     * - Configuration
     * - Default tyle.
     *
     * @param PhpWord $document
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
