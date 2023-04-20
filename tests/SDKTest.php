<?php

declare(strict_types=1);

require_once('vendor/autoload.php');

use PHPUnit\Framework\TestCase;

final class SDKTest extends TestCase
{
    protected function tearDown(): void
    {
        \Mockery::close();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testConstructorSuccess()
    {
        $httpRequestMock = \Mockery::mock('alias:\Uala\HttpRequest');
        $httpRequestMock->shouldReceive('post')
            ->with('https://auth.stage.ua.la/1/auth/token', [
                'user_name' => "fake_user",
                "client_id" => "fake_client_id",
                "client_secret_id" => "fake_secret_id",
                "grant_type" => "client_credentials"
            ])
            ->andReturn((object)[
                "body" => (object)[
                    "access_token" => "fake_access_token",
                    "expires_in" => 86400,
                    "token_type" => "Bearer"
                ],
                "status" => 200
            ]);

        $sdk = new Uala\SDK('fake_user', 'fake_client_id', 'fake_secret_id', true);
        $this->assertTrue(is_a($sdk, 'Uala\SDK'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws \Uala\Error
     */
    public function testCreateOrderSuccess()
    {
        $httpRequestMock = \Mockery::mock('alias:\Uala\HttpRequest');
        $httpRequestMock->shouldReceive('post')
            ->with('https://auth.stage.ua.la/1/auth/token', [
                'user_name' => "fake_user",
                "client_id" => "fake_client_id",
                "client_secret_id" => "fake_secret_id",
                "grant_type" => "client_credentials"
            ])
            ->andReturn((object)[
                "body" => (object)[
                    "access_token" => "fake_access_token",
                    "expires_in" => 86400,
                    "token_type" => "Bearer"
                ],
                "status" => 200
            ])->times(1);

        $httpRequestMock->shouldReceive('post')
            ->with('https://checkout.stage.ua.la/1/checkout', [
                'amount' => '10',
                "description" => 'test',
                "userName" => 'fake_user',
                "callback_fail" => 'https://www.google.com/',
                "callback_success" => 'https://www.google.com/',
                "notification_url" => null,
            ], ['Authorization: Bearer fake_access_token'])
            ->andReturn((object)[
                "body" => (object)[
                    "id" => "/api/v2/orders/e54be42d-043c-4bf3-9a4c-f85e37f13b3d",
                    "type" => "Order",
                    "uuid" => "e54be42d-043c-4bf3-9a4c-f85e37f13b3d",
                    "orderNumber" => "0004216-0000006391",
                    "currency" => "032",
                    "amount" => "10",
                    "status" => "PENDING",
                    "refNumber" => "ff7c3303-79bc-4b8f-b6bd-991c769e514f",
                    "links" => (object)[
                        "checkoutLink" => "https://checkout-uala.preprod.geopagos.com/orders/e54be42d-043c-4bf3-9a4c-f85e37f13b3d",
                        "success" => "https://www.google.com/success",
                        "failed" => "https://www.google.com/fail"
                    ]
                ],
                "status" => 200
            ])->times(1);

        $sdk = new Uala\SDK('fake_user', 'fake_client_id', 'fake_secret_id', true);
        $newOrder = $sdk->createOrder(10, 'test', 'https://www.google.com/', 'https://www.google.com/');
        $this->assertEquals('e54be42d-043c-4bf3-9a4c-f85e37f13b3d', $newOrder->uuid);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws \Uala\Error
     */
    public function testGetOrderSuccess()
    {
        $httpRequestMock = \Mockery::mock('alias:\Uala\HttpRequest');
        $httpRequestMock->shouldReceive('post')
            ->with('https://auth.stage.ua.la/1/auth/token', [
                'user_name' => "fake_user",
                "client_id" => "fake_client_id",
                "client_secret_id" => "fake_secret_id",
                "grant_type" => "client_credentials"
            ])
            ->andReturn((object)[
                "body" => (object)[
                    "access_token" => "fake_access_token",
                    "expires_in" => 86400,
                    "token_type" => "Bearer"
                ],
                "status" => 200
            ])->times(1);

        $httpRequestMock->shouldReceive('get')
            ->with(
                'https://checkout.stage.ua.la/1/order/e54be42d-043c-4bf3-9a4c-f85e37f13b3d',
                [],
                ['Authorization: Bearer fake_access_token']
            )
            ->andReturn((object)[
                "body" => (object)[
                    "order_id" => "e54be42d-043c-4bf3-9a4c-f85e37f13b3d",
                    "amount" => "10",
                    "status" => "PENDING",
                    "ref_number" => "ff7c3303-79bc-4b8f-b6bd-991c769e514f",
                ],
                "status" => 200
            ])->times(1);

        $sdk = new Uala\SDK('fake_user', 'fake_client_id', 'fake_secret_id', true);
        $order = $sdk->getOrder('e54be42d-043c-4bf3-9a4c-f85e37f13b3d');
        $this->assertEquals('e54be42d-043c-4bf3-9a4c-f85e37f13b3d', $order->order_id);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws \Uala\Error
     */
    public function testGetFailedNotificationSuccess()
    {
        $httpRequestMock = \Mockery::mock('alias:\Uala\HttpRequest');
        $httpRequestMock->shouldReceive('post')
            ->with('https://auth.stage.ua.la/1/auth/token', [
                'user_name' => "fake_user",
                "client_id" => "fake_client_id",
                "client_secret_id" => "fake_secret_id",
                "grant_type" => "client_credentials"
            ])
            ->andReturn((object)[
                "body" => (object)[
                    "access_token" => "fake_access_token",
                    "expires_in" => 86400,
                    "token_type" => "Bearer"
                ],
                "status" => 200
            ])->times(1);

        $httpRequestMock->shouldReceive('get')
            ->with('https://checkout.stage.ua.la/1/notifications', [], ['Authorization: Bearer fake_access_token'])
            ->andReturn((object)[
                "body" => (object)[
                    "notifications" => [
                        (object)[
                            "uuid" => "ff529666-9a1b-4919-8c06-8a21cf6ead5a",
                            "account_id" => "e5b801ef-06fb-40ed-8dc2-6bb2f26845bf",
                            "status_code" => 400,
                            "attempts" => 1,
                            "amount" => 10.21,
                            "created_date" => "2022-06-01T16:12:10Z"
                        ]
                    ]
                ],
                "status" => 200
            ])->times(1);

        $sdk = new Uala\SDK('fake_user', 'fake_client_id', 'fake_secret_id', true);
        $notifications = $sdk->getFailedNotifications();
        $this->assertEquals('ff529666-9a1b-4919-8c06-8a21cf6ead5a', $notifications[0]->uuid);
    }


    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws \Uala\Error
     */
    public function testGetOrdersSuccess()
    {
        $httpRequestMock = \Mockery::mock('alias:\Uala\HttpRequest');
        $httpRequestMock->shouldReceive('post')
            ->with('https://auth.stage.ua.la/1/auth/token', [
                'user_name' => "fake_user",
                "client_id" => "fake_client_id",
                "client_secret_id" => "fake_secret_id",
                "grant_type" => "client_credentials"
            ])
            ->andReturn((object)[
                "body" => (object)[
                    "access_token" => "fake_access_token",
                    "expires_in" => 86400,
                    "token_type" => "Bearer"
                ],
                "status" => 200
            ])->times(1);

        $httpRequestMock->shouldReceive('get')
            ->with(
                'https://checkout.stage.ua.la/1/order',
                ['limit' => 1, 'fromDate' => '', 'toDate' => ''],
                ['Authorization: Bearer fake_access_token']
            )
            ->andReturn((object)[
                "body" => (object)[
                    "orders" => [
                        (object)[
                            "order_id" => "ff529666-9a1b-4919-8c06-8a21cf6ead5a",
                            "amount" => 10,
                            "status" => "PENDING",
                            "ref_number" => "ff7c3303-79bc-4b8f-b6bd-991c769e514f",
                        ]
                    ]
                ],
                "status" => 200
            ])->times(1);

        $sdk = new Uala\SDK('fake_user', 'fake_client_id', 'fake_secret_id', true);
        $orders = $sdk->getOrders(1);
        $this->assertEquals('ff529666-9a1b-4919-8c06-8a21cf6ead5a', $orders[0]->order_id);
    }
}
