<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/FriendshipDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class FriendshipService extends BaseService
{
    private $userDao;

    public function __construct(?PDO $connection = null)
    {
        $this->dao = new FriendshipDao($connection);
        $this->userDao = new UserDao($connection);
    }

    protected function validateCreate(array $data)
    {
        parent::validateCreate($data);
        $this->requireFields($data, ['user_Id_1', 'user_Id_2', 'status']);
        $this->validateFields($data);
    }

    protected function validateUpdate(array $data)
    {
        parent::validateUpdate($data);
        $this->validateFields($data);
    }

    private function validateFields(array $data)
    {
        foreach (['user_Id_1', 'user_Id_2'] as $field) {
            if (isset($data[$field]) && filter_var($data[$field], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                throw new InvalidArgumentException('Invalid ' . $field . '.');
            }
            if (isset($data[$field]) && $this->userDao->findById($data[$field]) === null) {
                throw new InvalidArgumentException($field . ' does not reference an existing user.');
            }
        }
        if (isset($data['status']) && !in_array($data['status'], ['pending', 'accepted', 'blocked'], true)) {
            throw new InvalidArgumentException('Invalid friendship status.');
        }
    }
}
