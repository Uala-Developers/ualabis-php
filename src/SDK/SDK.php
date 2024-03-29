<?php

namespace Uala;

use Uala\HttpRequest;
use Uala\Error;

/**
 * Main class for Ualá Bis SDK
 * @author  Ualá Bis
 * @version 1.0.1
 * @package Uala
 */
class SDK
{
    /**
     * @var object
     */
    private $config;
    /**
     * @var string[]
     */
    private $apiBaseUrls = [
        'stage' => 'https://checkout.stage.ua.la/1',
        'production' => 'https://checkout.prod.ua.la/1',
    ];

    /**
     * @var string[]
     */
    private $authApiBaseUrls = [
        'stage' => 'https://auth.stage.ua.la/1/auth',
        'production' => 'https://auth.prod.ua.la/1/auth',
    ];

    /**
     * Config with your params to auth with Ualá Bis
     * @param string $username username from Ualá Bis credentials
     * @param string $clientId clientId from Ualá Bis credentials
     * @param string $clientSecret clientSecret from Ualá Bis credentials
     * @param bool $isDev determines the environment where the code will be executed, false by default
     * @return void
     */
    public function __construct($username, $clientId, $clientSecret, $isDev = false)
    {
        $this->config = (object)[
            'userName' => (string)$username,
            'clientId' => (string)$clientId,
            'clientSecret' => (string)$clientSecret,
            'baseUrl' => ($isDev) ? $this->apiBaseUrls['stage'] : $this->apiBaseUrls['production'],
            'authBaseUrl' => ($isDev) ? $this->authApiBaseUrls['stage'] : $this->authApiBaseUrls['production']
        ];
        $accessToken = $this->createAccessToken();
        $this->config->defaultHeaders = ['Authorization: Bearer ' . $accessToken];
    }

    /**
     * Generates a token with the credentials previously obtained in the constructor
     * @return access_token
     * @throws Error
     */
    private function createAccessToken()
    {
        $response = HttpRequest::post($this->config->authBaseUrl . '/token', [
            'user_name' => $this->config->userName,
            "client_id" => $this->config->clientId,
            "client_secret_id" => $this->config->clientSecret,
            "grant_type" => "client_credentials"
        ]);
        return $response->body->access_token;
    }

    /**
     * Create an order that you can use to make a sale.
     * @param float $amount order amount
     * @param string $description order description
     * @param string $callbackFail URL to redirect in case the payment of an order fails
     * @param string $callbackSuccess URL to redirect in case the payment of an order fails
     * @param string|null $notificationUrl URL endpoint to send status notifications. This is an OPTIONAL value
     * @return response
     * @throws Error
     * @link https://developers.ualabis.com.ar/orders/create-order/post
     */
    public function createOrder($amount, $description, $callbackFail, $callbackSuccess, $notificationUrl = null)
    {
        $response = HttpRequest::post($this->config->baseUrl . '/checkout', [
            'amount' => (string)$amount,
            "description" => $description,
            "userName" => $this->config->userName,
            "callback_fail" => $callbackFail,
            "callback_success" => $callbackSuccess,
            "notification_url" => $notificationUrl,
        ], $this->config->defaultHeaders);
        return $response->body;
    }

    /**
     * Gets a previously created order with unique identifier.
     * @param orderId $orderId the order id
     * @return response
     * @throws Error
     * @link https://developers.ualabis.com.ar/orders/get-order/get/order
     */
    public function getOrder($orderId)
    {
        $response = HttpRequest::get($this->config->baseUrl . "/order/$orderId", [], $this->config->defaultHeaders);
        return $response->body;
    }

    /**
     * Get a list of failed notifications via webhook that could not be completed with HTTP Status 200
     * @link https://developers.ualabis.com.ar/notifications/failed-notifications/failed
     * @return response
     * @throws Error
     */
    public function getFailedNotifications()
    {
        $response = HttpRequest::get($this->config->baseUrl . "/notifications", [], $this->config->defaultHeaders);
        return $response->body->notifications;
    }

    /**
     * Get a list of orders that be created
     * @param int|string|null $limit max number of orders to return - 10 by default.
     * @param string|null $fromDate date of creation of the orders from which the search will begin.
     * @param string|null $toDate date of creation of the orders until where it will be searched.
     * @return response
     * @throws Error
     * @link https://developers.ualabis.com.ar/orders/get-order/gets/orders
     */
    public function getOrders($limit = null, $fromDate = null, $toDate = null)
    {
        $response = HttpRequest::get(
            $this->config->baseUrl . "/order",
            ['limit' => $limit, 'fromDate' => $fromDate, 'toDate' => $toDate],
            $this->config->defaultHeaders
        );
        return $response->body->orders;
    }
}
