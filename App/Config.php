<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'db_dev';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'vide_grenier';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'dev_user';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'dev_password';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Get a configuration value from environment or fall back to the constant
     *
     * @param string $name     The environment variable name
     * @param mixed  $default  Fallback value if the variable is not set
     *
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        $value = getenv($name);
        return ($value !== false && $value !== '') ? $value : $default;
    }
}
