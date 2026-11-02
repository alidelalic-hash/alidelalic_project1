<?php

require_once __DIR__ . '/BaseDao.php';

class CvDao extends BaseDao
{
    public function __construct(?PDO $connection = null)
    {
        parent::__construct(
            'CVs',
            'id',
            ['name', 'status', 'created_at', 'created_by_user_Id', 'temp_id'],
            $connection
        );
    }
}
