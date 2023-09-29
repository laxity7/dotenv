<?php

namespace Laxity7\DotEnv\Test;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    /** @var array|bool */
    protected $originEnvs = false;
    protected $origin_Env = [];

    protected function loadOrigin()
    {
        $_ENV = $this->origin_Env;
        foreach ($this->getValidData() as $key => $value) {
            putenv($key);
        }
    }

    /** @inheritDoc */
    protected function setUp(): void
    {
        $this->originEnvs = getenv();
        $this->origin_Env = $_ENV;
    }

    /** @inheritDoc */
    protected function tearDown(): void
    {
        $this->loadOrigin();
    }

    /**
     * Valid data from test.env
     *
     * @return array
     */
    protected function getValidData(): array
    {
        return [
            'FOO_ENV' => 'production',
            'IS_FOO' => true,
            'BAR_DEBUG' => 'false',
            'FOO_HOST' => 'localhost',
            'FOO_NAME' => 'my_base',
            'FOO_USER' => 'root',
            'FOO_PASSWORD' => '123',
            'ROUND' => 0.01,
            'EPSILON' => 0.000000001,
            'FOO' => 'BAR BAZ BAR',
            'BAZ' => 'BAR BAZ BAR',
            'BAR' => null,
            'TEST' => null,
            'TEST_NULL' => null,
            'TEST_NULL_1' => null,
            'TEST_COMMENT' => 123,
            'TEST_COMMENT1' => '123',
            'TEST_COMMENT2' => '123',
            'TEST_COMMENT3' => '123 #it\'s no comment',
            'TEST_COMMENT4' => '123',
            'TEST_COMMENT5' => true,
            'TEST_COMMENT6' => '123 foo"bar',
            'bar_small' => 123,
        ];
    }

    /**
     * Get the private property at a class
     *
     * @param object|string $object
     * @param string $property
     * @param bool $isStatic
     *
     * @return mixed
     */
    public function getPrivateProperty($object, string $property, bool $isStatic = false)
    {
        $caller = function ($property) use ($isStatic) {
            return $isStatic ? static::${$property} : $this->$property;
        };

        if ($isStatic && is_string($object)) {
            $object = new $object();
        }

        return $caller->call($object, $property);
    }

    /**
     * Set the private property at a class
     *
     * @param object|string $object
     * @param string $property
     * @param mixed $value
     * @param bool $isStatic
     */
    public function setPrivateProperty($object, string $property, $value, bool $isStatic = false): void
    {
        $caller = function ($property) use ($value, $isStatic) {
            if ($isStatic) {
                static::${$property} = $value;
            } else {
                $this->$property = $value;
            }
        };

        if ($isStatic && is_string($object)) {
            $object = new $object();
        }

        $caller->call($object, $property);
    }
}
