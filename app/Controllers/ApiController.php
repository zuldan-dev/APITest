<?php

namespace App\Controllers;

use App\Models\{Users,Portfolios};
use App\Utils\CustomMethods;

/**
 * Class ApiController
 * @package App\Controllers
 */
class ApiController
{
    /**
     * @var array
     */
    private array $post;

    /**
     * @var Users
     */
    private Users $users;

    /**
     * @var Portfolios
     */
    private Portfolios $portfolios;

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->post = $_POST;
        $this->users = new Users();
        $this->portfolios = new Portfolios();
    }

    /**
     * User registration
     *
     * @return void
     */
    public function register(): void
    {
        $name = $this->post['name'] ?? '';
        $password = $this->post['password'] ?? '';
        $user = $this->users->read([ 'name' => $name ]);

        if (!empty($user['id'])) {
            $error = !empty($name) ? 'User ' . $name . ' has been created already' : 'User name is empty or incorrect';
            CustomMethods::renderError($error);
        } else {
            if ($this->users->create(['name' => $name, 'password' => $password])) {
                $token = $this->users->authenticate($name, $password);
                CustomMethods::renderData([ 'name' => $name ], $token);
            } else {
                CustomMethods::renderError('User ' . $name . ' can`t be created');
            }
        }
    }

    /**
     * Adds portfolio for current user
     *
     * @return void
     */
    public function add(): void
    {
        $portfolio = $this->post['portfolio'] ?? '';
        $token = $this->post['token'] ?? '';
        $userId = $this->users->validate($token);

        if ($userId > 0) {
            if (!empty($portfolio)) {
                $data = [];
                $portfolio = explode('\n', $portfolio);
                foreach ($portfolio as $row) {
                    if (empty($row)) {
                        continue;
                    }
                    $items = explode($_ENV['DATA_SEPARATOR'], $row);

                    if (count($items) < 3) {
                        continue;
                    }
                    $dataRow = [
                        'value' => $items[0],
                        'symbol' => $items[1],
                        'date' => $items[2],
                        'user_id' => $userId,
                    ];

                    if ($this->portfolios->create($dataRow)) {
                        $data[] = $dataRow;
                    }
                }
                CustomMethods::renderData($data, $token);
            } else {
                CustomMethods::renderError('Portfolio is empty');
            }
        } else {
            CustomMethods::renderError('Authorization error');
        }
    }

    /**
     * Gets portfolio for current user
     *
     * @return void
     */
    public function get(): void
    {
        $date = $this->post['date'] ?? '';
        $token = $this->post['token'] ?? '';
        $userId = $this->users->validate($token);

        if ($userId > 0) {
            $data = $this->portfolios->read([ 'date' => $date, 'user_id' => $userId ]);
            CustomMethods::renderData($data, $token);
        } else {
            CustomMethods::renderError('Authorization error');
        }
    }

    /**
     * Updating token time
     */
    public function login(): void
    {
        $name = $this->post['name'] ?: '';
        $password = $this->post['password'] ?: '';
        $token = $this->users->authenticate($name, $password);

        if (!empty($token)) {
            if ($this->users->update(['name' => $name], ['tokentime' => date('Y-m-d H:i:s')])) {
                CustomMethods::renderData([ 'name' => $name ], $token);
            } else {
                CustomMethods::renderError('User ' . $name . ' can`t be logged');
            }
        } else {
            CustomMethods::renderError('Password for user: ' . $name . ' is incorrect');
        }
    }

    /**
     * Reset token time
     *
     * @return void
     */
    public function logout(): void
    {
        $token = $this->post['token'] ?? '';
        $userId = $this->users->validate($token);

        if ($userId > 0) {
            $this->users->update(
                [
                    'id' => $userId,
                ],
                [
                    'tokentime' => date('Y-m-d H:i:s', strtotime('-' . $_ENV['TOKEN_TTL'] . ' seconds')),
                ]
            );
            CustomMethods::renderInfo('Token removed');
        } else {
            CustomMethods::renderInfo('Token already expired');
        }
    }
}
