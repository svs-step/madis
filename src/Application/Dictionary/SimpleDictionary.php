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

namespace App\Application\Dictionary;

use Knp\DictionaryBundle\Dictionary as DictionaryInterface;

class SimpleDictionary implements DictionaryInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $values;

    public function __construct(string $name, array $values)
    {
        $this->name   = $name;
        $this->values = $values;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getKeys(): array
    {
        return \array_keys($this->values);
    }

    public function offsetExists(mixed $offset): bool
    {
        return \array_key_exists($offset, $this->values);
    }

    public function offsetGet($offset): mixed
    {
        return $this->values[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->values[$offset]);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->values);
    }

    public function count(): int
    {
        return count($this->values);
    }
}
