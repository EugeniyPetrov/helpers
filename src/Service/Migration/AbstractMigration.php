<?php

namespace Eugeniypetrov\Lib\Service\Migration;

use \Doctrine\Migrations\AbstractMigration as DoctrineAbstractMigration;
use Eugeniypetrov\Lib\Service\Migration\Statement\CreateIndex;
use Eugeniypetrov\Lib\Service\Migration\Statement\DropIndex;

abstract class AbstractMigration extends DoctrineAbstractMigration
{
    /**
     * @param string $tableName
     * @param string $indexName
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function indexExists(string $tableName, string $indexName)
    {
        $stmt = $this->connection->prepare("
            SELECT TRUE FROM
                information_schema.STATISTICS
            WHERE
                TABLE_NAME = ?
                AND TABLE_SCHEMA = SCHEMA()
                AND INDEX_NAME = ?"
        );
        $stmt->execute([$tableName, $indexName]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * @param CreateIndex $statement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function ensureIndex(CreateIndex $statement)
    {
        if (!$this->indexExists($statement->getTableName(), $statement->getIndexName())) {
            $this->addSql(
                $this->toSql($statement)
            );
        }
    }

    /**
     * @param DropIndex $statement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function ensureNoIndex(DropIndex $statement)
    {
        if ($this->indexExists($statement->getTableName(), $statement->getIndexName())) {
            $this->addSql(
                $this->toSql($statement)
            );
        }
    }

    public function toSql($statement): string
    {
        $class = get_class($statement);
        switch ($class) {
            case CreateIndex::class:
                return $this->createIndexToSql($statement);
                break;
            case DropIndex::class:
                return $this->dropIndexToSql($statement);
                break;
            default:
                throw new \RuntimeException(sprintf("Unknown statement %s", $class));
        }
    }

    public function createIndexToSql(CreateIndex $statement)
    {
        $keyPart = [];
        foreach ($statement->getKeyPart() as $column) {
            $sql = sprintf(
                "`%s`",
                $column->getColumnName()
            );

            if ($column->getLength() !== null) {
                $sql .= sprintf("(%d)", $column->getLength());
            }

            if ($column->getOrder() !== null) {
                $sql .= sprintf(" %s", $column->getOrder());
            }

            $keyPart[] = $sql;
        }

        return sprintf(
            "CREATE INDEX `%s` ON `%s` (%s)",
            $statement->getIndexName(),
            $statement->getTableName(),
            join(", ", $keyPart)
        );
    }

    public function dropIndexToSql(DropIndex $statement)
    {
        return sprintf(
            "DROP INDEX `%s` ON `%s`",
            $statement->getIndexName(),
            $statement->getTableName()
        );
    }
}
