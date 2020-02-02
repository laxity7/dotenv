<?php
/**
 * Created by Vlad Varlamov (laxity.ru) on 25.12.2019.
 */
namespace Laxity7\Test;

use Laxity7\DotEnv;

class DotEnvTest extends BaseTestCase
{
    public const ENV_FILE = __DIR__ . '/test.env';

    public function testCorrectParse(): void
    {
        $env = new DotEnv();
        $env->load(self::ENV_FILE);

        foreach ($this->getValidData() as $key => $value) {
            $this->assertSame($value, $env->get($key), 'Error parse key: ' . $key);
            $this->assertSame($value, $_ENV[$key], 'Error parse key from $_ENV: ' . $key);
        }
    }

    public function testOverwrite(): void
    {
        $key = 'BAR';
        $_ENV[$key] = 'bar';

        $env = new DotEnv();
        $env->load(self::ENV_FILE);

        $this->assertSame('bar', $env->get($key), 'Overwritten key:' . $key);

        $this->loadOrigin();

        // now exist variable
        $env = new DotEnv();
        $env->load(self::ENV_FILE, true);

        $this->assertSame($this->getValidData()['BAR'], $env->get($key), 'Not overwritten key:' . $key);
    }

}
