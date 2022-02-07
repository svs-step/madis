<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author <chayrouse@datakode.fr>
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

namespace App\Domain\Documentation\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class DocumentTypeDictionary extends SimpleDictionary
{
    const TYPE_PDF         = 'pdf';
    const TYPE_MP4         = 'mp4';
    const TYPE_LINK        = 'url';
    const TYPE_DOCX        = 'docs';

    public function __construct()
    {
        parent::__construct('documentation_document_type', self::getTypes());
    }

    /**
     * Get an array of Objects.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_PDF          => 'PDF',
            self::TYPE_MP4           => 'Vidéos',
            self::TYPE_LINK         => 'Liens',
            self::TYPE_DOCX           => 'Documents',
        ];
    }

    /**
     * Get keys of the Objects array.
     *
     * @return array
     */
    public static function getTypesKeys()
    {
        return \array_keys(self::getTypes());
    }
}
