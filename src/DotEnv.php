<?php

declare(strict_types=1);

/**
 * @link      https://www.github.com/laxity7/dotenv
 * @copyright Copyright (c) 2023 Vlad Varlamov <vlad@varlamov.dev>
 * @license   https://opensource.org/licenses/MIT
 */

namespace Laxity7\DotEnv;

/**
 * Class DotEnv
 */
final class DotEnv
{
    /** @var array<string, string|bool|int|float|null> Environment variables */
    private array $envs = [];

    /**
     * @var bool If `putenv()` should be used to define environment variables or not.
     * Beware that `putenv()` is not thread safe, that's why this setting defaults to false
     */
    public bool $usePutEnv = false;

    /**
     * Load environment variables from a dotenv file
     *
     * @param string $dotEnvFilePath Path to a dotenv file. By default DOCUMENT_ROOT/.env
     * @param bool $overrideExistingVars Whether necessary to override existing environment variables
     * @param bool $usePutEnv Whether necessary to use putenv() to define environment variables
     *
     * @return void
     */
    public function load(string $dotEnvFilePath, bool $overrideExistingVars = false, bool $usePutEnv = false): void
    {
        $this->envs = array_merge(getenv(), $_ENV, $this->envs);

        if (!file_exists($dotEnvFilePath)) {
            return;
        }

        $contents = file_get_contents($dotEnvFilePath);
        if ($contents === false) {
            return;
        }

        $envRaw = preg_grep("/^[^#\s]+/i", explode("\n", $contents));
        if ($envRaw === false) {
            return;
        }

        foreach ($envRaw as $item) {
            $parts = explode('=', $item, 2);
            $name = trim($parts[0]);
            $rawValue = $parts[1] ?? '';

            // Support 'export' prefix
            if (strpos($name, 'export ') === 0) {
                $name = trim(substr($name, 7));
            }

            $value = $this->parseValue($rawValue);

            if ($overrideExistingVars || !array_key_exists($name, $this->envs)) {
                $this->envs[$name] = $value;
                $_ENV[$name] = $value;

                if ($this->usePutEnv || $usePutEnv) {
                    putenv("$name=$value");
                }
            }
        }
    }

    /**
     * Parse a raw value string into a typed value
     *
     * @param string $value Raw value string (everything after the = sign)
     *
     * @return string|bool|int|float|null Parsed value
     */
    private function parseValue(string $value)
    {
        $rawValue = trim($this->removeComments($value));
        $isSingleQuoted = strpos($rawValue, "'") === 0;
        $isDoubleQuoted = strpos($rawValue, '"') === 0;
        $value = trim($rawValue, ' "\'');

        // Interpolate ${VAR} references (not in single-quoted values)
        if (!$isSingleQuoted && strpos($value, '${') !== false) {
            $envs = $this->envs;
            $value = (string)preg_replace_callback('/\$\{([^}]+)\}/', static function ($matches) use ($envs) {
                if (!array_key_exists($matches[1], $envs)) {
                    return '';
                }
                $val = $envs[$matches[1]];
                if (is_bool($val)) {
                    return $val ? 'true' : 'false';
                }

                return (string)($val ?? '');
            }, $value);
        }

        switch (true) {
            case strpos($value, ' ') !== false || $isDoubleQuoted || $isSingleQuoted:
                $value = str_replace(["\'", '\"'], ["'", '"'], $value);
                break;
            case strtolower($value) === 'false' || strtolower($value) === 'true':
                $value = strtolower($value) === 'true';
                break;
            case strtolower($value) === 'null' || $rawValue === '':
                $value = null;
                break;
            case is_numeric($value):
                $value = strpos($value, '.') !== false ? (float)$value : (int)$value;
                break;
        }

        return $value;
    }

    /**
     * Check if an environment variable exists
     *
     * @param string $name The variable name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->envs);
    }

    /**
     * Clear all environment variables
     */
    public function clear(): void
    {
        $this->envs = [];
    }

    private function removeComments(string $string): string
    {
        if (strpos($string, '#') === false) {
            return $string;
        }

        $regexp = '/^((?:"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\')|[^\'"#]*)\s?(#.*)$/m';

        return (string)preg_replace($regexp, '$1', $string);
    }

    /**
     * Get the value of an environment variable
     *
     * @param string $varName The variable name.
     * @param string|bool|int|float|null $default Default value
     *
     * @return string|bool|int|float|null The value of the environment variable
     */
    public function get(string $varName, $default = null)
    {
        return $this->envs[$varName] ?? $default;
    }

    /**
     * Get all variables
     *
     * @return array<string, string|bool|int|float|null> Environment variables
     */
    public function getAll(): array
    {
        return $this->envs;
    }
}
