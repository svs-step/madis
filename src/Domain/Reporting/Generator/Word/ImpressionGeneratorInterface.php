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
    public function addSyntheticView(Section $section, array $data): void;

    public function addDetailedView(Section $section, array $data): void;
}
