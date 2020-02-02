<?php

/**
 * @link      https://www.github.com/laxity7/dotenv
 * @copyright Copyright (c) 2018 Vlad Varlamov <work@laxity.ru>
 * @license   https://opensource.org/licenses/MIT
 */

namespace Laxity7;

/**
 * Env class is a helper class for ease of access to variables
 */
class Env
{
    /** @var DotEnv Instance of DotEnv */
    static protected $env;

    /**
     * Load the initialized DotEnv class
     *
     * @param DotEnv $env
     */
    public static function load(DotEnv $env): void
    {
        static::$env = $env;
    }

    /**
     * Get the value of an environment variable
     *
     * @param string                     $varName The variable name.
     * @param string|bool|int|float|null $default Default value
     *
     * @return string|bool|int|float|null The value of the environment variable
     */
    public static function get(string $varName, $default = null)
    {
        if (static::$env === null) {
            $trace = debug_backtrace();
            trigger_error('You must first initialize the DotEnv class. Use the method "load" before. Error in file ' . $trace[0]['file'] . ' line ' . $trace[0]['line'], E_USER_WARNING);

            return $default;
        }

        return static::$env->get($varName, $default);
    }

}
