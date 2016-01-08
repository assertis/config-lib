<?php

namespace Assertis\Configuration\Drivers;

use Exception;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

/**
 * Provide lazy configuration from database
 *
 * @package Assertis\Configuration\Drivers
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class DatabaseDriver extends AbstractLazyDriver
{
    const SQL_GET_BY_KEY = "SELECT `value` FROM %s WHERE `key` = :key;";

    /**
     * @var PDO
     */
    private $db;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PDO $db
     * @param LoggerInterface $logger
     * @param string $tableName
     */
    public function __construct(PDO $db, LoggerInterface $logger, $tableName)
    {
        $this->db = $db;
        $this->tableName = $tableName;
        $this->logger = $logger;
    }

    /**
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        static $cache = [];

        if (!array_key_exists($key, $cache)) {
            $query = $this->executeQuery(sprintf(self::SQL_GET_BY_KEY, $this->tableName), compact('key'));

            if ($query->rowCount() < 1) {
                throw new UnexpectedValueException("Configuration record for key {$key} was not found.");
            }

            $cache[$key] = $query->fetchColumn(0);
        }

        return $cache[$key];
    }

    /**
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     * @throws Exception
     */
    private function executeQuery($sql, $params)
    {
        $query = $this->db->prepare($sql);

        if (!$query->execute($params)) {
            $errorInfo = $query->errorInfo();
            $error = "{$errorInfo['0']}/{$errorInfo['1']} - {$errorInfo[2]}";
            $json = json_encode($params);
            $this->logger->error("Could not execute query {$sql} with parameters {$json}: {$error}");
            throw new Exception("Could not execute SQL query.", 500);
        }

        return $query;
    }
}
