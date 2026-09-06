<?php
declare(strict_types=1);

// ITCS333 A4 — unit tests for src/validate.php. DO NOT MODIFY.
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/validate.php';

final class UnitTest extends TestCase
{
    public function test_required(): void
    {
        $this->assertFalse(validate_required(''));
        $this->assertFalse(validate_required('   '));
        $this->assertTrue(validate_required('a'));
        $this->assertTrue(validate_required(' Ahmed '));
    }

    public function test_email(): void
    {
        $this->assertFalse(validate_email(''));
        $this->assertFalse(validate_email('not-an-email'));
        $this->assertFalse(validate_email('a@b'));
        $this->assertFalse(validate_email('a b@c.com'));
        $this->assertTrue(validate_email('student@uob.edu.bh'));
        $this->assertTrue(validate_email('a.b+c@mail.co'));
    }

    public function test_length(): void
    {
        $this->assertFalse(validate_length('a', 2, 5));
        $this->assertTrue(validate_length('ab', 2, 5));
        $this->assertTrue(validate_length('abcde', 2, 5));
        $this->assertFalse(validate_length('abcdef', 2, 5));
        $this->assertTrue(validate_length('', 0, 0));
    }
}
