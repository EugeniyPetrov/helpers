<?php

require_once 'batch.php';

function pdo($name)
{
    $repo = new PdoRepository($_SERVER['HOME'] . '/.my.cnf.d');
    return $repo->getConnectionByName($name);
}

/**
 * @param string $filename
 * @return \PDO
 */
function sqlite($filename)
{
    return new \PDO("sqlite:" . $filename, null, null, [
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ]);
}

function lazy_pdo($name)
{
    return new LazyPdoConnection(function() use ($name) {
        return pdo($name);
    });
}

function elastic($name)
{
    $elastic = new ElasticRepository($_SERVER['HOME'] . '/.netrc');
    return $elastic->getElasticClient($name);
}

function rabbitmq()
{
    $ini = parse_ini_file($_SERVER['HOME'] . '/.rabbitmqadmin.conf', true);
    if (!array_key_exists('default', $ini)) {
        throw new \RuntimeException('No default section in rabbitmqadmin.conf');
    }

    $config = $ini['default'];

    return \RabbitMQ\Management\APIClient::factory([
        'host' => $config['hostname'],
        'port' => $config['port'],
        'username' => $config['username'],
        'password' => $config['password'],
    ]);
}

class FileRepository
{
    private $filesDirectory;

    /**
     * FileRepository constructor.
     * @param string $filesDirectory
     */
    public function __construct($filesDirectory)
    {
        if (!is_string($filesDirectory)) {
            throw new \InvalidArgumentException('`filesDirectory` is not string');
        }

        if (!file_exists($filesDirectory)) {
            throw new \InvalidArgumentException('`filesDirectory` does not exists');
        }

        if (!is_dir($filesDirectory)) {
            throw new \InvalidArgumentException('`filesDirectory` is not a directory');
        }

        $this->filesDirectory = $filesDirectory;
    }

    /**
     * @return mixed
     */
    public function getFilesDirectory()
    {
        return $this->filesDirectory;
    }
}

class PdoRepository extends FileRepository
{
    public function getConnectionByName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('`name` must be a string');
        }

        if (!preg_match('~^[0-9a-z_\-]+$~i', $name)) {
            throw new \InvalidArgumentException('Invalid characters in name');
        }

        $iniFile = $this->getFilesDirectory() . '/' . $name . '.cnf';
        if (!file_exists($iniFile)) {
            throw new \RuntimeException('Unable to find config file for ' . $iniFile);
        }

        $ini = parse_ini_file($iniFile, true);
        if (!array_key_exists('client', $ini)) {
            throw new \RuntimeException('No section client in ini file');
        }

        if (!is_array($ini['client'])) {
            throw new \RuntimeException('Wrong ini file format. client section is empty');
        }

        $connectionParams = [
            'host' => null,
            'user' => null,
            'password' => null,
            'database' => null,
            'default-character-set' => 'utf8mb4',
        ];

        foreach ($connectionParams as $key => $null) {
            if (!array_key_exists($key, $ini['client'])) {
                throw new \RuntimeException('No key `' . $key . '` found in client section of ini file');
            }

            $connectionParams[$key] = $ini['client'][$key];
        }

        return new \PDO(
            'mysql:host=' . $connectionParams['host'] . ';dbname=' . $connectionParams['database'] . ';charset=' . $connectionParams['default-character-set'],
            $connectionParams['user'],
            $connectionParams['password'], [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        );
    }
}

class LazyPdoConnection
{
    /**
     * @var \PDO
     */
    private $pdo;
    /**
     * @var callable
     */
    private $pdoConnector;

    /**
     * LazyPdoConnection constructor.
     * @param callable $pdoConnector
     */
    public function __construct(callable $pdoConnector)
    {
        $this->pdo = null;
        $this->pdoConnector = $pdoConnector;
    }

    protected function establishConnection()
    {
        $this->pdo = call_user_func($this->pdoConnector);
        return $this->pdo;
    }

    /**
     * @return \PDO
     */
    protected function getPdo()
    {
        if ($this->pdo === null) {
            return $this->establishConnection();
        } else {
            return $this->pdo;
        }
    }

    public function prepare($statement, array $driver_options = [])
    {
        return $this->getPdo()->prepare($statement, $driver_options);
    }

    public function beginTransaction()
    {
        return $this->getPdo()->beginTransaction();
    }

    public function commit()
    {
        return $this->getPdo()->commit();
    }

    public function rollBack()
    {
        return $this->getPdo()->rollBack();
    }

    public function inTransaction()
    {
        return $this->getPdo()->inTransaction();
    }

    public function setAttribute($attribute, $value)
    {
        return $this->getPdo()->setAttribute($attribute, $value);
    }

    public function exec($statement)
    {
        return $this->getPdo()->exec($statement);
    }

    public function query($statement)
    {
        return $this->getPdo()->query($statement);
    }

    public function lastInsertId($name = null)
    {
        return $this->getPdo()->lastInsertId($name);
    }

    public function errorCode()
    {
        return $this->getPdo()->errorCode();
    }

    public function errorInfo()
    {
        return $this->getPdo()->errorInfo();
    }

    public function getAttribute($attribute)
    {
        return $this->getPdo()->getAttribute($attribute);
    }

    public function quote($string, $parameter_type = PDO::PARAM_STR)
    {
        return $this->getPdo()->quote($string, $parameter_type);
    }

}

class Database
{
    /**
     * @var PDO
     */
    private $conn;

    /**
     * Database constructor.
     * @param \PDO $conn
     */
    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return string[]
     */
    public function getTables()
    {
        $tableRows = $this->conn->query("SHOW TABLES")->fetchAll();

        $tables = [];
        foreach ($tableRows as $tableRow) {
            $tableName = array_pop($tableRow);
            $tables[] = $tableName;
        }

        return $tables;
    }

    /**
     * @param string $tableName
     * @return array
     */
    public function getTablePk($tableName)
    {
        $stmt = $this->conn->prepare(
            "SELECT
              s.COLUMN_NAME,
              c.DATA_TYPE
            FROM
              information_schema.STATISTICS AS s
            LEFT JOIN
              information_schema.COLUMNS AS c ON
                c.TABLE_SCHEMA = s.TABLE_SCHEMA
                AND c.TABLE_NAME = s.TABLE_NAME
                AND c.COLUMN_NAME = s.COLUMN_NAME
            WHERE
              s.TABLE_NAME = :tableName
              AND s.TABLE_SCHEMA = SCHEMA()
              AND s.INDEX_NAME = 'PRIMARY'
            ORDER BY
              s.SEQ_IN_INDEX ASC"
        );

        $stmt->execute([
            'tableName' => $tableName,
        ]);
        $rows = $stmt->fetchAll();

        return $rows;
    }

    public function getPdo()
    {
        return $this->conn;
    }

    /**
     * @param string $sql
     * @return PDOStatement
     */
    public function query($sql)
    {
        //echo $this->conn->query("SELECT SCHEMA()")->fetchColumn() . ": " . $sql . "\n";
        return $this->conn->query($sql);
    }
}

class ElasticRepository
{
    /**
     * @var string
     */
    private $netrcFile;

    /**
     * ElasticRepository constructor.
     * @param string $netrcFile
     */
    public function __construct($netrcFile)
    {
        /* string validation. */
        if (!is_string($netrcFile)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid value of argument `%s`. Must be string but %s given', '$netrcFile', gettype($netrcFile))
            );
        }
        $this->netrcFile = $netrcFile;
    }

    /**
     * @param $pattern
     * @param int $port
     * @param string $proto
     * @return \Elasticsearch\Client
     */
    public function getElasticClient($pattern, $port = 9200, $proto = "http")
    {
        $lines = file($this->netrcFile);

        $hosts = [];
        foreach ($lines as $line) {
            $lineParts = preg_split('~\s+~', $line);
            if (fnmatch($pattern, $lineParts[1])) {
                $hosts[] = [
                    'scheme' => $proto,
                    'host' => $lineParts[1],
                    'port' => $port,
                    'user' => $lineParts[3],
                    'pass' => $lineParts[5],
                ];
            }
        }

        shuffle($hosts);

        return \Elasticsearch\ClientBuilder::create()
            ->setHosts($hosts)
            ->build();
    }
}
