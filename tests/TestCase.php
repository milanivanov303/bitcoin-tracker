<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var FakerGenerator
     */
    protected $faker;

    public function setUp() : void
    {
        parent::setUp();

        $this->faker = FakerFactory::create();
    }
}
