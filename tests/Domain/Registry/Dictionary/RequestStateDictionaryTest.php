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

namespace App\Tests\Domain\Registry\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\Registry\Dictionary\RequestStateDictionary;
use PHPUnit\Framework\TestCase;

class RequestStateDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new RequestStateDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new RequestStateDictionary();

        $this->assertEquals('registry_request_state', $dictionary->getName());
        $this->assertEquals(RequestStateDictionary::getStates(), $dictionary->getValues());
    }

    public function testGetStates()
    {
        $data = [
            RequestStateDictionary::STATE_AWAITING_CONFIRMATION      => 'En attente confirmation identité de la personne',
            RequestStateDictionary::STATE_ON_REQUEST                 => 'En demande de précision sur la demande',
            RequestStateDictionary::STATE_AWAITING_SERVICE           => 'En attente de réponse d\'un service',
            RequestStateDictionary::STATE_COMPLETED_CLOSED           => 'Demande traitée et clôturée',
            RequestStateDictionary::STATE_DENIED                     => 'Demande refusée',
        ];

        $this->assertEquals($data, RequestStateDictionary::getStates());
    }

    public function testGetStatesKeys()
    {
        $data = [
            RequestStateDictionary::STATE_AWAITING_CONFIRMATION,
            RequestStateDictionary::STATE_ON_REQUEST,
            RequestStateDictionary::STATE_AWAITING_SERVICE,
            RequestStateDictionary::STATE_COMPLETED_CLOSED,
            RequestStateDictionary::STATE_DENIED,
        ];

        $this->assertEquals($data, RequestStateDictionary::getStatesKeys());
    }
}
