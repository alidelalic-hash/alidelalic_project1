<?php

require_once __DIR__ . '/BaseDao.php';

class FriendshipDao extends BaseDao
{
    public function __construct(?PDO $connection = null)
    {
        parent::__construct('friendships', 'relation_id', ['user_Id_1', 'user_Id_2', 'status'], $connection);
    }
}
