<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CvDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/TemplateDao.php';

class CvService extends BaseService
{
    private $userDao;
    private $templateDao;

    public function __construct(?PDO $connection = null)
    {
        $this->dao = new CvDao($connection);
        $this->userDao = new UserDao($connection);
        $this->templateDao = new TemplateDao($connection);
    }

    protected function validateCreate(array $data)
    {
        parent::validateCreate($data);
        $this->requireFields($data, ['name', 'status', 'created_by_user_Id']);
        $this->validateFields($data);
    }

    protected function validateUpdate(array $data)
    {
        parent::validateUpdate($data);
        $this->validateFields($data);
    }

    private function validateFields(array $data)
    {
        if (isset($data['name']) && trim((string) $data['name']) === '') {
            throw new InvalidArgumentException('CV name cannot be empty.');
        }
        if (isset($data['status']) && !in_array($data['status'], ['draft', 'completed', 'shared'], true)) {
            throw new InvalidArgumentException('Invalid CV status.');
        }
        if (isset($data['created_by_user_Id']) && filter_var($data['created_by_user_Id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new InvalidArgumentException('Invalid created_by_user_Id.');
        }
        if (isset($data['created_by_user_Id'])
            && $this->userDao->findById($data['created_by_user_Id']) === null
        ) {
            throw new InvalidArgumentException('created_by_user_Id does not reference an existing user.');
        }
        if (array_key_exists('temp_id', $data) && $data['temp_id'] !== null) {
            if (filter_var($data['temp_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                throw new InvalidArgumentException('Invalid temp_id.');
            }
            if ($this->templateDao->findById($data['temp_id']) === null) {
                throw new InvalidArgumentException('temp_id does not reference an existing template.');
            }
        }
    }
}
