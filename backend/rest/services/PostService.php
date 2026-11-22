<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/PostDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class PostService extends BaseService
{
    private $userDao;

    public function __construct(?PDO $connection = null)
    {
        $this->dao = new PostDao($connection);
        $this->userDao = new UserDao($connection);
    }

    protected function validateCreate(array $data)
    {
        parent::validateCreate($data);
        $this->requireFields($data, ['title', 'body', 'user_id', 'status']);
        $this->validateFields($data);
    }

    protected function validateUpdate(array $data)
    {
        parent::validateUpdate($data);
        $this->validateFields($data);
    }

    private function validateFields(array $data)
    {
        if (isset($data['title']) && trim((string) $data['title']) === '') {
            throw new InvalidArgumentException('Title cannot be empty.');
        }
        if (isset($data['user_id']) && filter_var($data['user_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new InvalidArgumentException('Invalid user_id.');
        }
        if (isset($data['user_id']) && $this->userDao->findById($data['user_id']) === null) {
            throw new InvalidArgumentException('user_id does not reference an existing user.');
        }
        if (isset($data['status']) && !in_array($data['status'], ['draft', 'published', 'archived'], true)) {
            throw new InvalidArgumentException('Invalid post status.');
        }
    }
}
