<?php

namespace Eugeniypetrov\Lib\Service\Migration\Statement;

class IndexColumn
{
    /**
     * @var string
     */
    private $columnName;

    /**
     * @var null|integer
     */
    private $length;

    /**
     * @var null|string
     */
    private $order;

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * @param string $columnName
     * @return IndexColumn
     */
    public function setColumnName(string $columnName): self
    {
        $this->columnName = $columnName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * @param int|null $length
     * @return IndexColumn
     */
    public function setLength(?int $length): self
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrder(): ?string
    {
        return $this->order;
    }

    /**
     * @param string|null $order
     * @return IndexColumn
     */
    public function setOrder(?string $order): self
    {
        $this->order = $order;

        return $this;
    }
}
