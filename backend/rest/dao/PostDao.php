<?php

require_once __DIR__ . '/BaseDao.php';

class PostDao extends BaseDao
{
    public function __construct(?PDO $connection = null)
    {
        parent::__construct('posts', 'id', ['title', 'body', 'user_id', 'status', 'created_at'], $connection);
    }
}
