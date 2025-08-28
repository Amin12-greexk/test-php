<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific
| PHPUnit test case class. By default, that class is "PHPUnit\Framework\TestCase".
| Of course, you may need to change it using the "uses()" function to bind a
| different classes or traits.
|
*/

// Baris ini memberitahu semua tes di folder 'Feature' untuk menggunakan
// TestCase Laravel dan menjalankan RefreshDatabase secara otomatis.
uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain
| conditions. The "expect()" function gives you access to a set of powerful
| expectations and matchers that allow you to write elegant tests that read
| like natural language.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});