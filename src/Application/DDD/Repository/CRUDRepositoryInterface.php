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

namespace App\Application\DDD\Repository;

interface CRUDRepositoryInterface extends RepositoryInterface
{
    /**
     * Insert an object.
     *
     * @param mixed $object
     */
    public function insert($object): void;

    /**
     * Update an object.
     *
     * @param mixed $object
     */
    public function update($object): void;

    /**
     * Create an object.
     *
     * @return mixed
     */
    public function create();

    /**
     * Remove an object.
     *
     * @param mixed $object
     */
    public function remove($object): void;

    /**
     * Get all objects.
     *
     * @return mixed[]
     */
    public function findAll(): array;

    /**
     * Get an object by ID.
     *
     * @param string $id The ID to find
     *
     * @return mixed[]
     */
    public function findOneById(string $id);
}
