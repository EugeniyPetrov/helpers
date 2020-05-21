<?php

namespace Eugeniypetrov\Lib\Service\Migration\Statement;

class DropIndex
{
    /**
     * @var string
     */
    private $indexName;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @return string
     */
    public function getIndexName(): string
    {
        return $this->indexName;
    }

    /**
     * @param string $indexName
     * @return DropIndex
     */
    public function setIndexName(string $indexName): self
    {
        $this->indexName = $indexName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     * @return DropIndex
     */
    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }
}
