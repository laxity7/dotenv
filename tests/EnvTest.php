<?php
/**
 * Created by Vlad Varlamov (laxity.ru) on 25.12.2019.
 */

namespace Laxity7\Test;

use Laxity7\DotEnv;
use Laxity7\Env;

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

        $this->assertSame($this->getPrivateProperty(Env::class, 'env', true), $env, 'Error load env class');
    }

    /**
     * @expectedException \PHPUnit\Framework\Error\Error
     */
    public function testErrorGet(): void
    {
        $this->expectExceptionMessageRegExp('/You must first initialize the DotEnv class\. Use the method "load" before.*/');
        Env::get('FOO');
    }

    public function testCorrectGet(): void
    {
        $env = new DotEnv();
        $env->load(self::ENV_FILE);

        Env::load($env);

        $this->assertSame($this->getValidData()['BAR'], Env::get('BAR'), 'Not overwritten key:');
    }

}
