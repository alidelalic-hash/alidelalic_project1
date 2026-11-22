<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';

class UserService extends BaseService
{
    public function __construct(?PDO $connection = null)
    {
        $this->dao = new UserDao($connection);
    }

    protected function validateCreate(array $data)
    {
        parent::validateCreate($data);
        $this->requireFields($data, ['name', 'role', 'username']);
        $this->validateFields($data);
    }

    protected function validateUpdate(array $data)
    {
        parent::validateUpdate($data);
        $this->validateFields($data);
    }

    private function validateFields(array $data)
    {
        if (isset($data['role']) && !in_array($data['role'], ['admin', 'moderator', 'member'], true)) {
            throw new InvalidArgumentException('Invalid role.');
        }
        if (isset($data['username']) && trim((string) $data['username']) === '') {
            throw new InvalidArgumentException('Username cannot be empty.');
        }
    }
}
