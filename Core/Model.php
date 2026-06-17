<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class Model
{

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::get('DB_HOST', Config::DB_HOST) . ';dbname=' . Config::get('DB_NAME', Config::DB_NAME) . ';charset=utf8';
            $db = new PDO($dsn, Config::get('DB_USER', Config::DB_USER), Config::get('DB_PASSWORD', Config::DB_PASSWORD));

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }
}
