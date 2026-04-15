# PHP DotEnv

A fast, lightweight `.env` file parser for PHP. Loads environment variables and makes them accessible
via `$_ENV`, `getenv()`, and the built-in `Env::get()` helper.

[![License](https://img.shields.io/github/license/laxity7/dotenv.svg)](https://github.com/laxity7/dotenv/blob/master/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/laxity7/dotenv.svg)](https://packagist.org/packages/laxity7/dotenv)
[![Total Downloads](https://img.shields.io/packagist/dt/laxity7/dotenv.svg)](https://packagist.org/packages/laxity7/dotenv)

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Features](#features)
    - [Type Casting](#type-casting)
    - [Variable Interpolation](#variable-interpolation)
    - [Export Prefix](#export-prefix)
    - [Inline Comments](#inline-comments)
    - [Error Tolerance](#error-tolerance)
- [API](#api)
    - [DotEnv](#dotenv)
    - [Env Helper](#env-helper)
- [Comparison with symfony/dotenv](#comparison-with-symfonydotenv)
- [Development](#development)

## Requirements

- PHP **>=7.4 | 8+**

## Installation

```shell
composer require laxity7/dotenv
```

## Quick Start

Create a `.env` file in your project root (make sure it is added to `.gitignore`):

```dotenv
APP_NAME=MyApp
APP_DEBUG=true
DB_HOST=localhost
DB_PORT=3306
```

Load it in your application:

```php
$dotenv = new \Laxity7\DotEnv\DotEnv();
$dotenv->load(__DIR__ . '/.env');

$dotenv->get('APP_NAME');  // 'MyApp'
$dotenv->get('APP_DEBUG'); // true (boolean)
$dotenv->get('DB_PORT');   // 3306 (integer)
$dotenv->get('MISSING', 'default'); // 'default'
```

All loaded variables are also available in `$_ENV`:

```php
$_ENV['APP_NAME']; // 'MyApp'
```

> If the file does not exist, no error is thrown — only already existing environment variables will be available.

You can load additional files. By default, existing variables are **not** overwritten:

```php
$dotenv->load(__DIR__ . '/.env');
$dotenv->load(__DIR__ . '/.env.local');       // will not overwrite existing variables
$dotenv->load(__DIR__ . '/.env.local', true); // will overwrite existing variables
```

### Helper Function

For convenience, you can create a global `env()` helper:

```php
function env(string $name, $default = null) {
    static $dotenv = null;

    if ($dotenv === null) {
        $dotenv = new \Laxity7\DotEnv\DotEnv();
        $dotenv->load(__DIR__ . '/.env');
    }

    return $dotenv->get($name, $default);
}
```

Or use the built-in `Env` static helper:

```php
$dotenv = new \Laxity7\DotEnv\DotEnv();
$dotenv->load(__DIR__ . '/.env');
\Laxity7\DotEnv\Env::load($dotenv);

// Now available anywhere:
\Laxity7\DotEnv\Env::get('APP_NAME');           // 'MyApp'
\Laxity7\DotEnv\Env::get('MISSING', 'default'); // 'default'
\Laxity7\DotEnv\Env::has('APP_NAME');            // true
```

Or combine both approaches for a short `env()` function:

```php
$dotenv = new \Laxity7\DotEnv\DotEnv();
$dotenv->load(__DIR__ . '/.env');
\Laxity7\DotEnv\Env::load($dotenv);

function env(string $name, $default = null) {
    return \Laxity7\DotEnv\Env::get($name, $default);
}
```

## Features

### Type Casting

Values are automatically cast to native PHP types:

| .env value              | PHP type    | PHP value    |
|-------------------------|-------------|--------------|
| `FOO=hello`             | `string`    | `'hello'`    |
| `FOO=123`               | `int`       | `123`        |
| `FOO=3.14`              | `float`     | `3.14`       |
| `FOO=.5`                | `float`     | `0.5`        |
| `FOO=true` / `FOO=false`| `bool`      | `true`/`false` |
| `FOO=null`              | `null`      | `null`       |
| `FOO=`                  | `null`      | `null`       |
| `FOO="true"`            | `string`    | `'true'`     |
| `FOO="123"`             | `string`    | `'123'`      |

> **Note:** Quoted values (`"..."` or `'...'`) are always treated as strings, even if they look like numbers or booleans.

### Variable Interpolation

Reference previously defined variables using `${VAR}` syntax:

```dotenv
APP_NAME=MyApp
APP_ENV=production
APP_TITLE="${APP_NAME} (${APP_ENV})"

DB_HOST=localhost
DB_PORT=3306
DB_NAME=mydb
DATABASE_URL="mysql://${DB_HOST}:${DB_PORT}/${DB_NAME}"
```

```php
$dotenv->get('APP_TITLE');    // 'MyApp (production)'
$dotenv->get('DATABASE_URL'); // 'mysql://localhost:3306/mydb'
```

> Interpolation is **not** performed inside single-quoted values:
> `FOO='${BAR}'` will remain the literal string `${BAR}`.  
> If a referenced variable is not defined, it is replaced with an empty string.

### Export Prefix

Lines prefixed with `export` are supported (common in shell-compatible `.env` files):

```dotenv
export APP_ENV=production
export APP_DEBUG=false
```

The `export` keyword is simply stripped — variables are loaded as usual.

### Inline Comments

Comments are supported both on their own line and inline:

```dotenv
# This is a full-line comment
FOO=123 # This is an inline comment
BAR="value #not a comment" # But this is
```

### Error Tolerance

Unlike many parsers, this package gracefully handles common formatting issues:

```dotenv
FOO =123       # spaces around key
BAR= 456       # space before value
BAZ            # missing = sign (treated as null)
FOOS=foo bar   # unquoted value with spaces
```

All of the above will be parsed without errors.

## API

### DotEnv

```php
$dotenv = new \Laxity7\DotEnv\DotEnv();
```

#### `load(string $path, bool $overrideExistingVars = false, bool $usePutEnv = false): void`

Load variables from a `.env` file.

- `$path` — path to the `.env` file. If the file does not exist, the call is silently ignored.
- `$overrideExistingVars` — if `true`, variables from the file will overwrite already defined ones.
- `$usePutEnv` — if `true`, also call `putenv()` for each variable (see below).

#### `get(string $name, $default = null): mixed`

Get the value of an environment variable. Returns `$default` if the variable is not defined.

#### `has(string $name): bool`

Check whether a variable exists (including variables with a `null` value).

#### `getAll(): array`

Return all loaded environment variables as an associative array.

#### `clear(): void`

Clear the internal variable store. Does **not** modify `$_ENV` or system environment — only resets the internal state.

#### `$usePutEnv` (property)

```php
$dotenv->usePutEnv = true;
$dotenv->load(__DIR__ . '/.env');
```

When enabled, variables are also registered via `putenv()`, making them accessible through `getenv()`.

> ⚠️ `putenv()` is **not thread-safe**. Use only when you specifically need `getenv()` support.

The `$usePutEnv` parameter in `load()` serves the same purpose but applies only to that single call,
while the property applies to all subsequent `load()` calls.

### Env Helper

A static helper class for convenient access from anywhere in your application.

```php
\Laxity7\DotEnv\Env::load($dotenv); // initialize with a DotEnv instance
\Laxity7\DotEnv\Env::get('KEY', 'default');
\Laxity7\DotEnv\Env::has('KEY');
```

> Calling `get()` or `has()` before `load()` will throw a `\RuntimeException`.

## Comparison with symfony/dotenv

| Feature                    | laxity7/dotenv  | symfony/dotenv |
|----------------------------|-----------------|----------------|
| Native boolean casting     | ✅               | ❌              |
| Native int/float casting   | ✅               | ❌              |
| Error tolerance            | ✅               | ❌              |
| Inline comments            | ✅               | ❌              |
| Variable interpolation     | ✅               | ✅              |
| `export` prefix            | ✅               | ✅              |
| Performance                | **~4× faster**  | baseline       |

### Boolean Values

```dotenv
FOO=true
BAR=false
BAZ="false"
```

```php
// laxity7/dotenv
$dotenv->get('FOO'); // true  (bool)
$dotenv->get('BAR'); // false (bool)
$dotenv->get('BAZ'); // 'false' (string — it's quoted)

// symfony/dotenv — all values are strings
$_ENV['FOO']; // 'true' (string)
```

### Performance

Benchmark results (10 000 iterations):

| Metric | laxity7/dotenv | symfony/dotenv |
|--------|----------------|----------------|
| min    | 0.0218 ms      | 0.0773 ms      |
| max    | 0.3831 ms      | 0.9657 ms      |
| avg    | 0.0238 ms      | 0.0967 ms      |

Run benchmarks yourself:

```shell
make bench
# or
composer bench
```

### Error Tolerance

This `.env` file is valid for laxity7/dotenv, but causes exceptions in symfony/dotenv:

```dotenv
FOO =123
BAR= 456
BAZ
FOOS=foo bar baz
```

symfony/dotenv errors:
```
Whitespace characters are not supported after the variable name in ".env" at line 1.
Whitespace are not supported before the value in ".env" at line 2.
Missing = in the environment variable declaration in ".env" at line 3.
A value containing spaces must be surrounded by quotes in ".env" at line 4
```

## Development

The project uses Docker for all commands, so no local PHP installation is required.

```shell
make install   # Install composer dependencies
make test      # Run PHPUnit tests
make phpstan   # Run PHPStan static analysis (level 9 + strict rules)
make bench     # Run benchmark comparison (laxity7 vs symfony)
make check     # Run tests + PHPStan
```

## License

[MIT](LICENSE)
