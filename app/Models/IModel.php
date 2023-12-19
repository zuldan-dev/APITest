<?php

namespace App\Models;

/**
 * Interface IModel
 * @package App\Models
 */
interface IModel
{
    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data = []): bool;

    /**
     * @param array $params
     * @return array
     */
    public function read(array $params = []): array;

    /**
     * @param array $params
     * @param array $data
     * @return bool
     */
    public function update(array $params = [], array $data = []): bool;
}
