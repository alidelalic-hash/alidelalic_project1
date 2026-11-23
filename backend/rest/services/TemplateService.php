<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/TemplateDao.php';

class TemplateService extends BaseService
{
    public function __construct(?PDO $connection = null)
    {
        $this->dao = new TemplateDao($connection);
    }

    protected function validateCreate(array $data)
    {
        parent::validateCreate($data);
        $this->requireFields($data, ['name']);
    }

    protected function validateUpdate(array $data)
    {
        parent::validateUpdate($data);
        if (isset($data['name']) && trim((string) $data['name']) === '') {
            throw new InvalidArgumentException('Template name cannot be empty.');
        }
    }
}
