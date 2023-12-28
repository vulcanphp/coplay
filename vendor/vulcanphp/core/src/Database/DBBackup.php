<?php

namespace VulcanPhp\Core\Database;

use Exception;
use VulcanPhp\SimpleDb\Exceptions\DatabaseException;
use PDOException;

class DBBackup
{
    /**
     * Create a new Database Backup Manager Instance
     * @return void 
     * @throws Exception 
     */
    public function __construct()
    {
        if (!is_mysql()) {
            throw new Exception('This Backup Feature Only Support MySQL Engine');
        }
    }

    /**
     * Export Database Backup Sql Dump files
     * 
     * @param mixed $tables 
     * @param array $except 
     * @param array $except_data 
     * @return string 
     * @throws DatabaseException 
     * @throws PDOException 
     */
    public function dump($tables, $except = [], $except_data = []): string
    {
        if (is_string($tables) && $tables === '*') {

            $query = database()->prepare('SHOW TABLES');
            $query->execute();

            $tables = [];

            while ($row = $query->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            $query->closeCursor();
        }

        $dump = "-- database backup - " . date('Y-m-d H:i:s') . PHP_EOL;
        $dump .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';" . PHP_EOL;
        $dump .= "SET FOREIGN_KEY_CHECKS = 0;" . PHP_EOL;

        foreach ((array) $tables as $table) {

            if (in_array($table, (array) $except)) {
                continue;
            }

            // drop table
            $dump .= <<<EOT
            --
            -- Table structure for table `{$table}`
            --

            DROP TABLE IF EXISTS `$table`;
            EOT . PHP_EOL;

            // create table
            $query2 = database()->prepare("SHOW CREATE TABLE `$table`");
            $query2->execute();

            $row2 = $query2->fetch(\PDO::FETCH_NUM);
            $query2->closeCursor();

            $dump .= PHP_EOL . $row2[1] . ";" . PHP_EOL . PHP_EOL . <<<EOT
            
            --
            -- Dumping data for table `{$table}`
            --
            EOT . PHP_EOL . PHP_EOL;

            if (!in_array($table, (array) $except_data)) {

                // dump files
                $query = database()->prepare("SELECT * FROM `$table`");
                $query->execute();
                $values = array();

                while ($row = $query->fetch(\PDO::FETCH_NUM)) {
                    $column = "";

                    for ($j = 0; $j < count($row); $j++) {
                        $val  = isset($row[$j]) ? addslashes($row[$j]) : null;
                        $column .= $val !== null ? "'" . $val . "', " : "'', ";
                    }

                    $values[] = "(" . rtrim($column, ', ') . ")";
                }

                if (!empty($values)) {
                    $dump .= "INSERT INTO `$table` VALUES " . implode(', ', $values) . ";" . PHP_EOL;
                }
            }

            $dump .= <<<EOT
            -- --------------------------------------------------------
            EOT . PHP_EOL . PHP_EOL;
        }

        $dump .= 'SET FOREIGN_KEY_CHECKS = 1;' . PHP_EOL . PHP_EOL;
        $dump .= 'COMMIT;';

        return $dump;
    }

    /**
     * Import Database Backup Dump File
     * @param string $dump 
     * @return mixed 
     */
    public function import(string $dump)
    {
        return database()->exec($dump);
    }
}
