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

namespace App\Domain\Registry\Repository;

use App\Application\DDD\Repository\RepositoryInterface;
use App\Domain\User\Model\Collectivity;

interface Proof extends RepositoryInterface
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
     * @param bool $deleted
     *
     * @return mixed[]
     */
    public function findAll(bool $deleted = false): array;

    /**
     * Get an object by ID.
     *
     * @param string $id The ID to find
     *
     * @return mixed[]
     */
    public function findOneById(string $id);

    /**
     * Find all proofs by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param bool         $deleted      Get deleted rows or not
     * @param array        $order        Order results
     *
     * @return array The array of proofs given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity, bool $deleted = false, array $order = []);

    /**
     * Find all proofs by criteria.
     *
     * @param array $criteria List of criteria
     *
     * @return array The array of proofs given by criteria
     */
    public function findBy(array $criteria = []);
}
