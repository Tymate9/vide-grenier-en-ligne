<?php

namespace App;

class Config
{
    const DB_HOST = 'db_dev';
    const DB_NAME = 'vide_grenier';
    const DB_USER = 'dev_user';
    const DB_PASSWORD = 'dev_password';
    const SHOW_ERRORS = true;

    public static function get($name, $default = null)
    {
        $value = getenv($name);
        return ($value !== false && $value !== '') ? $value : $default;
    }
}
