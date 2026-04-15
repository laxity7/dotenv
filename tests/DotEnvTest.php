<?php

namespace Laxity7\DotEnv\Test;

use Laxity7\DotEnv\DotEnv;

class DotEnvTest extends BaseTestCase
{
    public const ENV_FILE = __DIR__ . '/test.env';

    public function testCorrectParse(): void
    {
        $env = new DotEnv();
        $env->load(self::ENV_FILE);

        foreach ($this->getValidData() as $key => $value) {
            self::assertSame($value, $env->get($key), 'Error parse key: ' . $key);
            self::assertSame($value, $_ENV[$key], 'Error parse key from $_ENV: ' . $key);
        }
    }

    public function testOverwrite(): void
    {
        $key = 'BAR';
        $_ENV[$key] = 'bar';

        $env = new DotEnv();
        $env->load(self::ENV_FILE);

        self::assertSame('bar', $env->get($key), 'Overwritten key:' . $key);

        $this->loadOrigin();

        // now exist variable
        $env = new DotEnv();
        $env->load(self::ENV_FILE, true);

        self::assertSame($this->getValidData()['BAR'], $env->get($key), 'Not overwritten key:' . $key);
    }

    public function testGetAll(): void
    {
        $env = new DotEnv();
        $env->load(self::ENV_FILE);
        $envs = $env->getAll();

        self::assertNotEmpty($envs);

        foreach ($this->getValidData() as $key => $value) {
            self::assertArrayHasKey($key, $envs);
            self::assertSame($value, $envs[$key]);
        }
    }

    public function testHas(): void
    {
        $env = new DotEnv();
        $env->load(self::ENV_FILE);

        self::assertTrue($env->has('FOO_ENV'));
        self::assertTrue($env->has('BAR')); // BAR=null, but exists
        self::assertFalse($env->has('COMPLETELY_UNDEFINED_VAR'));
    }

    public function testNonExistentFile(): void
    {
        $env = new DotEnv();
        $env->load(__DIR__ . '/non_existent.env');

        // Should not throw, should still have system envs
        self::assertIsArray($env->getAll());
    }

    public function testUsePutEnv(): void
    {
        $env = new DotEnv();
        $env->load(self::ENV_FILE, false, true);

        self::assertSame('production', getenv('FOO_ENV'));
    }

    public function testUsePutEnvProperty(): void
    {
        $env = new DotEnv();
        $env->usePutEnv = true;
        $env->load(self::ENV_FILE);

        self::assertSame('production', getenv('FOO_ENV'));
    }

}
