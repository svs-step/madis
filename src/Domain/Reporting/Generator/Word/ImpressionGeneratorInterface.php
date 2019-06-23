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

use PhpOffice\PhpWord\Element\Section;

interface ImpressionGeneratorInterface
{
    /**
     * Add synthetic information to display in PHPWord document.
     *
     * @param Section $section
     * @param array   $data
     */
    public function addSyntheticView(Section $section, array $data): void;

    /**
     * Add detailed information to display in PHPWord document for each data.
     *
     * @param Section $section
     * @param array   $data
     */
    public function addDetailedView(Section $section, array $data): void;
}
