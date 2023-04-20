<?php
namespace App\Models;

/**
 * Interface IModel
 * @package App\Models
 */
interface IModel {

    public function create( array $data = [] ): bool;

    public function read( array $params = [] ): array;

    public function update( array $params = [], array $data = [] ): bool;
}
