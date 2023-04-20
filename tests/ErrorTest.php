<?php

declare(strict_types=1);

require_once('vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Uala\Error;

final class ErrorTest extends TestCase
{
    protected function tearDown(): void
    {
        \Mockery::close();
    }

    public function testErrorUserNotFound()
    {

        $e = new Error('User account not found', '3003', 401);
        $this->assertEquals('user_no_exist', $e->getErrorType());
        $this->assertEquals('User account not found', $e->getMessage());
        $this->assertEquals(401, $e->getStatusCode());
    }
}
