<?php

namespace App\Models;

/**
 * Class Portfolios
 * @package App\Models
 */
class Portfolios extends Model implements IModel
{
    /**
     * Portfolios constructor.
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * Portfolio creating
     * @param array $data
     * @return bool
     */
    public function create(array $data = []): bool
    {
        if (empty($data)) {
            return false;
        }
        // needs to move to rules config
        $availableSymbols = ['$','%','&','@'];

        if (!in_array($data['symbol'], $availableSymbols)) {
            return false;
        }
        //date validation
        if (isset($data['date'])) {
            $data['date'] = date('Y-m-d H:i:s', strtotime($data['date']));
        }
        $insert = $this->pdo->prepare('insert into portfolios ( ' . implode(',', array_keys($data)) .
            ' ) values ( ' . implode(',', array_fill(0, count($data), '?')) . ')');
        $insert->execute(array_values($data));

        return true;
    }

    /**
     * Portfolio reading
     * @param array $params
     * @return array
     */
    public function read(array $params = []): array
    {
        $result = [];

        if (empty($params)) {
            return $result;
        }

        if (!empty($params['user_id'])) {
            return $result;
        }

        if (empty($params['date'])) {
            $result = $this->getLast($params['user_id']);
        } else {
            $date = date('Y-m-d H:i:s', strtotime($params['date']));
            $select = $this->pdo->prepare('select * from portfolios where user_id = ? and date >= ?');
            $select->execute([ $params['user_id'], $date ]);
            $result = $select->fetchAll($this->pdo::FETCH_ASSOC);

            if (empty($result)) {
                $result = $this->getLast($params['user_id']);
            }
        }

        return $result;
    }

    /**
     * Portfolio updating
     * @param array $params
     * @param array $data
     * @return bool
     */
    public function update(array $params = [], array $data = []): bool
    {
        // TODO: Implement update() method.
        return true;
    }

    /**
     * Gets newest portfolio record by user
     * @param int $userId
     * @return array
     */
    private function getLast(int $userId): array
    {
        $select = $this->pdo->prepare('select * from portfolios where user_id = ? order by date desc limit 1');
        $select->execute([ $userId ]);

        return $select->fetch($this->pdo::FETCH_ASSOC);
    }
}
