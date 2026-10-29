<?php
/*
 * Configuration TEMPLATE.
 *
 * 1. Copy this file to config.php (same folder).
 * 2. Put your own values into config.php.
 *
 * config.php is listed in .gitignore and must NEVER be committed,
 * because it contains secrets (database password, JWT secret, API keys).
 * This example file IS committed so everybody knows which settings exist.
 */
class Config
{
    public static function DB_HOST()     { return self::get_env('DB_HOST', '127.0.0.1'); }
    public static function DB_PORT()     { return self::get_env('DB_PORT', 3306); }
    public static function DB_NAME()     { return self::get_env('DB_NAME', 'cv_social_db'); }
    public static function DB_USER()     { return self::get_env('DB_USER', 'root'); }
    public static function DB_PASSWORD() { return self::get_env('DB_PASSWORD', ''); }

    /**
     * Environment variables (set on the server) win over the default values above.
     */
    public static function get_env($name, $default)
    {
        $value = getenv($name);
        return ($value !== false && trim($value) !== '') ? $value : $default;
    }
}