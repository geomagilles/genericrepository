<?php
/**
 * This file is part of the GenericRepository framework.
 *
 * (c) Gilles Barbier <geomagilles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geomagilles\GenericRepository;

/**
 * Interface ModelRepositoryInterface
 */
interface GenericRepositoryInterface
{
    //
    // INSTANCE METHOD
    //

    /**
     * Update current entity
     * 
     * @param array $data
     */
    public function update($data = []);

    /**
     * Save current entity
     * 
     * @return boolean
     */
    public function save();

    /**
     * Return entity's id
     * @return mixed
     */
    public function getId();

    //
    // CLASS METHOD
    //

    /**
     * Gets table name 
     * @return string
     */
    public function getTable();

    /**
     * Wraps one or more models into a modelRepository 
     * @param $data
     * @return ModelRepository|ModelRepository[]
     */
    public function wrap($data);

    /**
     * Create a new entity
     * 
     * @param array $data
     * @return ModelRepositoryInterface
     */
    public function create(array $data = []);

    /**
     * Delete an entity
     * 
     */
    public function delete();

    /**
     * Return all entities
     *
     * @return GenericRepositoryInterface[]
     */
    public function getAll();

    /**
     * Find an entity by Id
     *
     * @param $id
     * @param array $with
     * @return GenericRepositoryInterface|null
     */
    public function getById($id, array $with = array());

    /**
     * Get Results by Page
     *
     * @param int $page
     * @param int $limit
     * @param array $with
     * @return StdClass Object with $items and $totalItems for pagination
     */
    public function getByPage($page = 1, $limit = 10, array $with = array());

    /**
     * Delete an entity by id
     * 
     * @param $id
     * @param array $with
     * @return boolean|null
     */
    public function deleteById($id, array $with = array());

    /**
     * Find a single entity by key value
     *
     * @param string $key
     * @param string $value
     * @param array $with
     * @return GenericRepositoryInterface|null
     */
    public function getFirstBy($key, $value, array $with = array());

    /**
     * Find many entities by key value
     *
     * @param string $key
     * @param string $value
     * @param array $with
     */
    public function getManyBy($key, $value, array $with = array());

    /**
     * Delete a single entity by key value
     *
     * @param string $key
     * @param string $value
     * @return boolean|null
     */
    public function deleteFirstBy($key, $value);

    /**
     * Delete many entities by key value
     *
     * @param string $key
     * @param string $value
     * @return boolean|null
     */
    public function deleteManyBy($key, $value);
}
