<?php

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
    /** @var array Environment variables */
    private array $envs = [];

    /**
     * @var bool If `putenv()` should be used to define environment variables or not.
     * Beware that `putenv()` is not thread safe, that's why this setting defaults to false
     */
    public bool $usePutEnv = false;

    /**
     * Get the value of an environment variable
     *
     * @param string|null $dotEnvFilePath Path to a dotenv file. By default DOCUMENT_ROOT/.env
     * @param bool $overrideExistingVars Whether necessary to override existing environment variables
     *
     * @return void The value of the environment variable
     */
    public function load(string $dotEnvFilePath, bool $overrideExistingVars = false): void
    {
        $this->envs = getenv() + $_ENV;

        if (!file_exists($dotEnvFilePath)) {
            return;
        }

        $envRaw = preg_grep("/^[^#\s]+/i", explode("\n", file_get_contents($dotEnvFilePath)));

        foreach ($envRaw as $item) {
            [$name, $value] = explode('=', $item . '=');
            $name = trim($name);
            $rawValue = trim($this->removeComments($value ?? 'null'));
            $value = trim($rawValue, ' "\'');
            switch (true) {
                case strpos($value, ' ') || strpos($rawValue, '"') === 0 || strpos($rawValue, "'") === 0:
                    $value = str_replace(["\'", '\"'], ["'", '"'], $value);
                    break;
                case strtolower($value) === 'false' || strtolower($value) === 'true':
                    $value = strtolower($value) === 'true';
                    break;
                case strtolower($value) === 'null' || $rawValue === '':
                    $value = null;
                    break;
                case is_numeric($value):
                    $value = strpos($value, '.') ? (float)$value : (int)$value;
                    break;
            }

            if ($overrideExistingVars || !array_key_exists($name, $this->envs)) {
                $this->envs[$name] = $value;
                $_ENV[$name] = $value;

                if ($this->usePutEnv) {
                    putenv("$name=$value");
                }
            }
        }
    }

    private function removeComments(string $string): string
    {
        if (strpos($string, '#') === false) {
            return $string;
        }

        $regexp = '/^((?:"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\')|[^\'"#]*)\s?(#.*)$/m';

        return preg_replace($regexp, '$1', $string);
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
     * @return array Environment variables
     */
    public function getAll(): array
    {
        return $this->envs;
    }
}
