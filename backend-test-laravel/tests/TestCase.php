<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // PASTIKAN ANDA MEMILIKI BARIS INI:
    use CreatesApplication;
}