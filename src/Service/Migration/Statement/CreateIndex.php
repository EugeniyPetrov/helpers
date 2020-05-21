<?php

namespace Eugeniypetrov\Lib\Service\Migration\Statement;

class CreateIndex
{
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    /**
     * @var string
     */
    private $indexName;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var IndexColumn[]
     */
    private $keyPart;

    /**
     * @return string
     */
    public function getIndexName(): string
    {
        return $this->indexName;
    }

    /**
     * @param string $indexName
     * @return CreateIndex
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
     * @return CreateIndex
     */
    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @return IndexColumn[]
     */
    public function getKeyPart(): array
    {
        return $this->keyPart;
    }

    /**
     * @param IndexColumn[] $keyPart
     * @return CreateIndex
     */
    public function setKeyPart(array $keyPart): self
    {
        $this->keyPart = $keyPart;

        return $this;
    }
}
