<?php

use DragonCode\Benchmark\Benchmark;
use Laxity7\DotEnv\Test\Benchmark\TestBench;

require_once __DIR__ . '/../../vendor/autoload.php';

// preloading classes for benchmark
$test = new TestBench();
$test->benchLaxity7DotEnvLoad();
$test->benchSymfonyDotEnvLoad();

(new Benchmark())
    ->iterations(10000)
    ->round(4)
    ->compare([
        'Laxity7' => static fn() => $test->benchLaxity7DotEnvLoad(),
//        'override' => static fn() => $test->benchLaxity7DotEnvLoadWithOverride(),
        'Symfony' => static fn() => $test->benchSymfonyDotEnvLoad(),
    ]);
