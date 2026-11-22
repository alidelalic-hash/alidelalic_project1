<?php

abstract class BaseService
{
    protected $dao;

    public function getAll()
    {
        return $this->dao->findAll();
    }

    public function getById($id)
    {
        $this->validateId($id);
        return $this->dao->findById($id);
    }

    public function create(array $data)
    {
        $this->validateCreate($data);
        $id = $this->dao->create($data);
        return $this->dao->findById($id);
    }

    public function update($id, array $data)
    {
        $this->validateId($id);
        $this->validateUpdate($data);
        if ($this->dao->findById($id) === null) {
            return null;
        }
        $this->dao->update($id, $data);
        return $this->dao->findById($id);
    }

    public function delete($id)
    {
        $this->validateId($id);
        if ($this->dao->findById($id) === null) {
            return false;
        }
        return $this->dao->delete($id);
    }

    protected function validateId($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new InvalidArgumentException('Invalid record ID.');
        }
    }

    protected function requireFields(array $data, array $fields)
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                throw new InvalidArgumentException('Missing required field: ' . $field);
            }
        }
    }

    protected function validateCreate(array $data)
    {
        if ($data === []) {
            throw new InvalidArgumentException('Request body must contain at least one field.');
        }
    }

    protected function validateUpdate(array $data)
    {
        if ($data === []) {
            throw new InvalidArgumentException('Request body must contain at least one field.');
        }
    }
}
