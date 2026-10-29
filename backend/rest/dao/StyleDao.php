<?php

require_once __DIR__ . '/BaseDao.php';

class StyleDao extends BaseDao
{
    public function __construct(?PDO $connection = null)
    {
        parent::__construct('styles', 'id', ['name', 'description'], $connection);
    }
}
