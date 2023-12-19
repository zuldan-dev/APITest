<?php

namespace App\Models;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Class Users
 * @package App\Models
 */
class Users extends Model implements IModel
{
    /**
     * Users constructor.
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * Users creating, returns token after correct operation
     *
     * @param array $data
     *
     * @return bool
     */
    public function create(array $data = []): bool
    {
        if (empty($data)) {
            return false;
        }

        if (array_key_exists('password', $data) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $insert = $this->pdo->prepare('INSERT INTO users ( ' . implode(',', array_keys($data)) .
        ' ) VALUES ( ' . implode(',', array_fill(0, count($data), '?')) . ')');
        $insert->execute(array_values($data));

        return true;
    }

    /**
     * Users reading
     *
     * @param array $params
     *
     * @return array
     */
    public function read(array $params = []): array
    {
        $result = [];
        $queryParams = [];

        if (empty($params)) {
            return $result;
        }

        foreach ($params as $param => $value) {
            $queryParams[] = $param . ' = ?';
        }

        $select = $this->pdo->prepare('select * from users where ' . implode(' and ', $queryParams));
        $select->execute(array_values($params));
        $result = $select->fetch($this->pdo::FETCH_ASSOC);

        return is_array($result) ? $result : [];
    }

    /**
     * Users updating
     *
     * @param array $params
     * @param array $data
     *
     * @return bool
     */
    public function update(array $params = [], array $data = []): bool
    {
        $queryParams = [];
        $queryData = [];

        if (empty($params) && empty($data)) {
            return false;
        }
        foreach ($params as $param => $value) {
            $queryParams[] = $param . '=:' . $param;
        }
        foreach ($data as $param => $value) {
            $queryData[] = $param . '=:' . $param;
        }
        $this->pdo->prepare('UPDATE users SET ' . implode(', ', $queryData) . ' WHERE ' .
        implode(', ', $queryParams))->execute(array_merge($data, $params));

        return true;
    }

    /**
     * Creates token from name and password
     *
     * @param string $name
     * @param string $password
     *
     * @return string
     */
    public function authenticate(string $name, string $password): string
    {
        if (empty($name) && empty($password)) {
            return '';
        }
        $user = $this->read(['name' => $name]);

        if (isset($user['password']) && password_verify($password, $user['password'])) {
            $jwt = JWT::encode([ 'name' => $name ], $_ENV['JWT_KEY'], $_ENV['JWT_ALG']);

            return $jwt;
        } else {
            return '';
        }
    }

    /**
     * Validate token with ttl, return user_id
     *
     * @param string $token
     *
     * @return int
     */
    public function validate(string $token): int
    {
        if (empty($token)) {
            return 0;
        }
        $decoded = JWT::decode($token, new Key($_ENV['JWT_KEY'], $_ENV['JWT_ALG']));

        if (!$decoded->name) {
            return 0;
        }
        $user = $this->read(['name' => $decoded->name]);
        $period = strtotime(date('Y-m-d H:i:s')) - strtotime($user['tokentime']);
        // Ignoring expired tokens
        $id = ( $period < $_ENV['TOKEN_TTL'] ) ? $user['id'] : 0;

        return $id;
    }
}
