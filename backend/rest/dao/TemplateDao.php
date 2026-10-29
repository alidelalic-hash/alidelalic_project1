<?php

require_once __DIR__ . '/BaseDao.php';

class TemplateDao extends BaseDao
{
    public function __construct(?PDO $connection = null)
    {
        parent::__construct('templates', 'id', ['name', 'description'], $connection);
    }
}
