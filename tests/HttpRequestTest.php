<?php
declare(strict_types=1);

require_once('vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class HttpRequestTest extends TestCase
{

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGet()
    {
        $url = 'http://localhost:3000/get-example';
        $request = Uala\HttpRequest::get($url, [''], ['']);
        $this->assertEquals(200, $request->status);
    }

    public function testGetWithError()
    {
        $this->expectException(\Uala\Error::class);
        $url = 'http://localhost:3000/get-example-error';
        $request = Uala\HttpRequest::get($url, [''], ['']);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPost()
    {
        $url = 'http://localhost:3000/post-example';
        $request = Uala\HttpRequest::post($url, [''], ['']);
        $this->assertEquals(201, $request->status);
    }
}
