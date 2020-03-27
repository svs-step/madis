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

namespace App\Tests\Domain\Reporting\Generator\Csv;

use App\Domain\Reporting\Generator\Csv\AbstractGenerator;
use App\Domain\Reporting\Generator\Csv\GeneratorInterface;
use PHPUnit\Framework\TestCase;

class ExportCsvHandlerTest extends TestCase
{
    /**
     * @var FooCsvGenerator
     */
    private $generator;

    protected function setUp()
    {
        $this->generator = new FooCsvGenerator();
    }

    public function testItIsAnInstanceOfGeneratorInterfaceGeneratorInferface()
    {
        $this->assertInstanceOf(GeneratorInterface::class, $this->generator);
    }
}

class FooCsvGenerator extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    public function initializeExtract(): array
    {
        return [['header1'], ['data1']];
    }
}
