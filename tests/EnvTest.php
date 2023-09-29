<?php
/**
 * Created by Vlad Varlamov (varlamov.dev) on 25.12.2019.
 */

namespace Laxity7\DotEnv\Test;

use Laxity7\DotEnv\DotEnv;
use Laxity7\DotEnv\Env;

class EnvTest extends BaseTestCase
{
    public const ENV_FILE = __DIR__ . '/test.env';

    /** @inheritDoc */
    protected function loadOrigin()
    {
        parent::loadOrigin();
        $this->setPrivateProperty(Env::class, 'env', null, true);
    }

    public function testLoad(): void
    {
        $env = new DotEnv();
        $env->load(self::ENV_FILE);

        Env::load($env);

        self::assertSame($this->getPrivateProperty(Env::class, 'env', true), $env, 'Error load env class');
    }

    public function testErrorGet(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Env class not initialized');
        Env::get('FOO');
    }

    public function testCorrectGet(): void
    {
        $env = new DotEnv();
        $env->load(self::ENV_FILE);

        Env::load($env);

        self::assertSame($this->getValidData()['BAR'], Env::get('BAR'), 'Not overwritten key:');
    }

}
