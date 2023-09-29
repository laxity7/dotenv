<?php

namespace Laxity7\DotEnv\Test\Benchmark;

class TestBench
{

    /**
     * @Revs(10000)
     * @Iterations(5)
     */
    public function benchLaxity7DotEnvLoad(): void
    {
        $_ENV = [];
        $env = new \Laxity7\DotEnv\DotEnv();
        $env->load(__DIR__ . '/../test.env');
    }

    /**
     * @Revs(10000)
     * @Iterations(5)
     */
    public function benchLaxity7DotEnvLoadWithOverride(): void
    {
        $_ENV = [];
        $env = new \Laxity7\DotEnv\DotEnv();
        $env->load(__DIR__ . '/../test.env', true);
    }

    /**
     * @Revs(10000)
     * @Iterations(5)
     */
    public function benchSymfonyDotEnvLoad(): void
    {
        $_ENV = [];
        $env = new \Symfony\Component\Dotenv\Dotenv();
        $env->load(__DIR__ . '/../test_for_symfony.env');
    }
}
