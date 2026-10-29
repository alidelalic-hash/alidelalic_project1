<?php

require_once __DIR__ . '/Database.php';

class BaseDao
{
    protected $connection;
    private $table;
    private $primaryKey;
    private $fillableColumns;

    public function __construct($table, $primaryKey, array $fillableColumns, ?PDO $connection = null)
    {
        $this->table = $this->validateIdentifier($table);
        $this->primaryKey = $this->validateIdentifier($primaryKey);
        $this->fillableColumns = array_map(
            [$this, 'validateIdentifier'],
            $fillableColumns
        );
        $this->connection = $connection === null ? Database::getConnection() : $connection;
    }

    public function findAll()
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM ' . $this->quoteIdentifier($this->table)
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findById($id)
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM ' . $this->quoteIdentifier($this->table)
            . ' WHERE ' . $this->quoteIdentifier($this->primaryKey) . ' = :id'
        );
        $statement->execute(['id' => $id]);
        $record = $statement->fetch();

        return $record === false ? null : $record;
    }

    public function create(array $data)
    {
        $data = $this->filterData($data);
        if ($data === []) {
            throw new InvalidArgumentException('At least one field is required to create a record.');
        }

        $columns = array_keys($data);
        $quotedColumns = array_map([$this, 'quoteIdentifier'], $columns);
        $placeholders = array_map(
            static function ($column) {
                return ':' . $column;
            },
            $columns
        );

        $sql = 'INSERT INTO ' . $this->quoteIdentifier($this->table)
            . ' (' . implode(', ', $quotedColumns) . ')'
            . ' VALUES (' . implode(', ', $placeholders) . ')';
        $statement = $this->connection->prepare($sql);
        $statement->execute($data);

        return (int) $this->connection->lastInsertId();
    }

    public function update($id, array $data)
    {
        $data = $this->filterData($data);
        if ($data === []) {
            throw new InvalidArgumentException('At least one field is required to update a record.');
        }

        $assignments = [];
        $parameters = ['id' => $id];
        foreach ($data as $column => $value) {
            $parameter = 'set_' . $column;
            $assignments[] = $this->quoteIdentifier($column) . ' = :' . $parameter;
            $parameters[$parameter] = $value;
        }

        $sql = 'UPDATE ' . $this->quoteIdentifier($this->table)
            . ' SET ' . implode(', ', $assignments)
            . ' WHERE ' . $this->quoteIdentifier($this->primaryKey) . ' = :id';
        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        if ($statement->rowCount() > 0) {
            return true;
        }

        return $this->findById($id) !== null;
    }

    public function delete($id)
    {
        $statement = $this->connection->prepare(
            'DELETE FROM ' . $this->quoteIdentifier($this->table)
            . ' WHERE ' . $this->quoteIdentifier($this->primaryKey) . ' = :id'
        );
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    private function filterData(array $data)
    {
        foreach ($data as $column => $value) {
            if (!in_array($column, $this->fillableColumns, true)) {
                throw new InvalidArgumentException('Field is not writable: ' . $column);
            }
        }

        return $data;
    }

    private function validateIdentifier($identifier)
    {
        if (!is_string($identifier) || !preg_match('/\A[a-zA-Z_][a-zA-Z0-9_]*\z/', $identifier)) {
            throw new InvalidArgumentException('Invalid SQL identifier.');
        }

        return $identifier;
    }

    private function quoteIdentifier($identifier)
    {
        return '`' . $this->validateIdentifier($identifier) . '`';
    }
}
