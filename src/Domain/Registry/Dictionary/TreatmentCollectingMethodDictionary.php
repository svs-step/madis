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

namespace App\Domain\Registry\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class TreatmentCollectingMethodDictionary extends SimpleDictionary
{
    public const METHOD_WEB_FORM          = 'web_form';
    public const METHOD_PAPER_FORM        = 'paper_form';
    public const METHOD_CONTRACT          = 'contract';
    public const METHOD_RECEIVED_LIST     = 'received_list';
    public const METHOD_INTERNAL_DOCUMENT = 'internal_document';
    public const METHOD_ORAL_INTERVIEW    = 'oral_interview';
    public const METHOD_OTHER             = 'other';

    public function __construct()
    {
        parent::__construct('registry_treatment_collecting_method', self::getMethods());
    }

    /**
     * Get an array of Basis.
     *
     * @return array
     */
    public static function getMethods()
    {
        return [
            self::METHOD_WEB_FORM          => 'Formulaire web',
            self::METHOD_PAPER_FORM        => 'Formulaire papier',
            self::METHOD_CONTRACT          => 'Contrat',
            self::METHOD_RECEIVED_LIST     => 'Liste reçue',
            self::METHOD_INTERNAL_DOCUMENT => 'Document interne',
            self::METHOD_ORAL_INTERVIEW    => 'Entretien oral',
            self::METHOD_OTHER             => 'Autre',
        ];
    }

    /**
     * Get keys of the Basis array.
     *
     * @return array
     */
    public static function getMethodKeys()
    {
        return \array_keys(self::getMethods());
    }
}
