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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class AbstractGenerator implements GeneratorInterface
{
    public function generateResponse(string $documentName): BinaryFileResponse
    {
        $currentDate = (new \DateTimeImmutable())->format('Ymd');
        $fileName    = "{$currentDate}-{$documentName}.csv";
        $tempFile    = \tempnam(\sys_get_temp_dir(), $fileName);

        $outputBuffer = fopen($tempFile, 'w');
        fprintf($outputBuffer, chr(0xEF) . chr(0xBB) . chr(0xBF)); // need this line to support accent on Excel
        foreach ($this->initializeExtract() as $row) {
            fputcsv($outputBuffer, $row, ';', '"');
        }
        fclose($outputBuffer);

        // Create response and return it
        $response = new BinaryFileResponse($tempFile);
        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }

    /**
     * Format a date for Word document.
     *
     * @param \DateTimeInterface|null $dateTime The date to parse
     *
     * @throws \Exception
     *
     * @return string The parsed date with the good timezone
     */
    protected function getDate(?\DateTimeInterface $dateTime, ?string $format = null): string
    {
        if (\is_null($dateTime)) {
            return '';
        }

        $format = $format ?? self::DATE_TIME_FORMAT;

        $parsedDateTime = new \DateTimeImmutable();
        $parsedDateTime = $parsedDateTime->setTimestamp($dateTime->getTimestamp());
        $parsedDateTime = $parsedDateTime->setTimezone(new \DateTimeZone(self::DATE_TIME_ZONE));

        return $parsedDateTime->format($format);
    }
}
