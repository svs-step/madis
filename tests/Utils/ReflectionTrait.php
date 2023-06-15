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

namespace App\Tests\Utils;

trait ReflectionTrait
{
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on
     * @param string $methodName Method name to call
     * @param array  $parameters array of parameters to pass into method
     *
     * @throws \ReflectionException
     *
     * @return mixed method return
     */
    protected function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(\get_class($object));
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Call protected/private static method of a class.
     *
     * @param string $class      Class name that we will run method on
     * @param string $methodName Method name to call
     * @param array  $parameters array of parameters to pass into method
     *
     * @throws \ReflectionException
     *
     * @return mixed method return
     */
    protected function invokeStaticMethod(string $class, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass($class);
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs(null, $parameters);
    }

    /**
     * Sets a protected property on a given object via reflection.
     *
     * @param $object   - instance in which protected value is being modified
     * @param $property - property on instance being modified
     * @param $value    - new value of the property being modified
     *
     * @throws \ReflectionException
     */
    protected function setProtectedProperty(&$object, $property, $value)
    {
        $reflectionClass    = new \ReflectionClass(\get_class($object));
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * Sets a protected method on a given object via reflection.
     *
     * @param $object - instance in which protected value is being modified
     * @param $method - property on instance being modified
     * @param $value  - new value of the property being modified
     *
     * @throws \ReflectionException
     *
     * @return \ReflectionClass
     */
    protected function setProtectedMethod(&$object, $method, $value)
    {
        $reflectionClass    = new \ReflectionClass(\get_class($object));
        $reflectionProperty = $reflectionClass->getMethod($method);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);

        return $reflectionClass;
    }
}
