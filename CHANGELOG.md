# Changelog

All notable changes to this project will be documented in this file.

## [1.0.4] - 2025-04-15

- Added `has()` method on `DotEnv` and `Env` classes
- Added variable interpolation (`${VAR}` syntax)
- Added support for `export` prefix in .env files
- Added PHPStan (level 6 + strict-rules), GitHub Actions CI, Makefile
- Fixed parsing of float values with leading dot (`.5`)
- Fixed values containing `=` are now preserved (`FOO=bar=baz`)
- Fixed namespace typos in README
- Changed `Env` to throw `\RuntimeException` instead of `\Error` when not initialized
- Added `declare(strict_types=1)` in all source files
