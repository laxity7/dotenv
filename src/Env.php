<?php

declare(strict_types=1);

/**
 * @link      https://www.github.com/laxity7/dotenv
 * @copyright Copyright (c) 2023 Vlad Varlamov <vlad@varlamov.dev>
 * @license   https://opensource.org/licenses/MIT
 */

namespace Laxity7\DotEnv;

use RuntimeException;

/**
 * Env class is a helper class for ease of access to variables
 */
class Env
{
    /** @var DotEnv|null Instance of DotEnv */
    private static ?DotEnv $env = null;

    /**
     * Load the initialized DotEnv class
     *
     * @param DotEnv $env
     */
    public static function load(DotEnv $env): void
    {
        self::$env = $env;
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
        if (!isset(self::$env)) {
            throw new RuntimeException('Env class not initialized');
        }

        return self::$env->get($varName, $default);
    }

    /**
     * Check if an environment variable exists
     *
     * @param string $name The variable name
     *
     * @return bool
     */
    public static function has(string $name): bool
    {
        if (!isset(self::$env)) {
            throw new RuntimeException('Env class not initialized');
        }

        return self::$env->has($name);
    }
}
