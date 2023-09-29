<?php

/**
 * @link      https://www.github.com/laxity7/dotenv
 * @copyright Copyright (c) 2023 Vlad Varlamov <vlad@varlamov.dev>
 * @license   https://opensource.org/licenses/MIT
 */

namespace Laxity7\DotEnv;

use Error;

/**
 * Env class is a helper class for ease of access to variables
 */
class Env
{
    /** @var DotEnv|null Instance of DotEnv */
    static private ?DotEnv $env;

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
     * @param string $varName The variable name.
     * @param string|bool|int|float|null $default Default value
     *
     * @return string|bool|int|float|null The value of the environment variable
     */
    public static function get(string $varName, $default = null)
    {
        if (!isset(static::$env)) {
            throw new Error('Env class not initialized');
        }

        return static::$env->get($varName, $default);
    }

}
