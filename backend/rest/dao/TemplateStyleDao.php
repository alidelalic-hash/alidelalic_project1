<?php

require_once __DIR__ . '/BaseDao.php';

class TemplateStyleDao extends BaseDao
{
    public function __construct(?PDO $connection = null)
    {
        parent::__construct('template_styles', 'id', ['temp_id', 'style_id'], $connection);
    }
}
