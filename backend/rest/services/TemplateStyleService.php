<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/TemplateStyleDao.php';
require_once __DIR__ . '/../dao/TemplateDao.php';
require_once __DIR__ . '/../dao/StyleDao.php';

class TemplateStyleService extends BaseService
{
    private $templateDao;
    private $styleDao;

    public function __construct(?PDO $connection = null)
    {
        $this->dao = new TemplateStyleDao($connection);
        $this->templateDao = new TemplateDao($connection);
        $this->styleDao = new StyleDao($connection);
    }

    protected function validateCreate(array $data)
    {
        parent::validateCreate($data);
        $this->requireFields($data, ['temp_id', 'style_id']);
        $this->validateFields($data);
    }

    protected function validateUpdate(array $data)
    {
        parent::validateUpdate($data);
        $this->validateFields($data);
    }

    private function validateFields(array $data)
    {
        foreach (['temp_id', 'style_id'] as $field) {
            if (isset($data[$field])
                && filter_var($data[$field], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false
            ) {
                throw new InvalidArgumentException('Invalid ' . $field . '.');
            }
        }
        if (isset($data['temp_id']) && $this->templateDao->findById($data['temp_id']) === null) {
            throw new InvalidArgumentException('temp_id does not reference an existing template.');
        }
        if (isset($data['style_id']) && $this->styleDao->findById($data['style_id']) === null) {
            throw new InvalidArgumentException('style_id does not reference an existing style.');
        }
    }
}
