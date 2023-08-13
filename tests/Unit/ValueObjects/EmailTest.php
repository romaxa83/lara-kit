<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\Email;
use InvalidArgumentException;
use stdClass;
use Tests\TestCase;
use TypeError;

class EmailTest extends TestCase
{
    public function test_it_create_success(): void
    {
        $emailString = 'valid.email@example.com';
        $email = new Email($emailString);

        self::assertEquals($emailString, $email);
    }

    public function test_it_has_exception_when_create_by_not_valid_email_string(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $email = new Email('not.valid.email@example');
    }

    public function test_it_compare_same_objects_success(): void
    {
        $emailString = 'valid.email@example.com';
        $email1 = new Email($emailString);
        $email2 = new Email($emailString);

        self::assertTrue($email1->compare($email2));
    }

    public function test_it_compare_not_equals_object_fail(): void
    {
        $email1 = new Email('valid.email1@example.com');
        $email2 = new Email('valid.email2@example.com');

        self::assertFalse($email1->compare($email2));
    }

    public function test_it_has_exception_when_compare_not_same_objects(): void
    {
        $email = new Email('valid.email1@example.com');
        $object = new stdClass();

        $this->expectException(TypeError::class);

        $email->compare($object);
    }
}
