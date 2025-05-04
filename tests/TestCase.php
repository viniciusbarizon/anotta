<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Indicates whether the database should be seeded before tests are run.
     *
     * @var bool
     */
    protected $seed = true;
}
