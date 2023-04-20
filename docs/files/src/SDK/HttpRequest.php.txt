<?php

namespace Uala;

use Uala\Error;

class HttpRequest
{
    private static $defaultConfig = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_HEADER => true,
    ];

    private static $defaultHeaders = [
        'Content-Type: application/json',
    ];

    private static function getHeaders($headers)
    {
        return array_merge(self::$defaultHeaders, $headers);
    }

    /**
     * @throws Error
     */
    private static function execute($curl)
    {
        $response = curl_exec($curl);

        $body = substr($response, curl_getinfo($curl, CURLINFO_HEADER_SIZE));
        $body = (object)json_decode($body);

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($statusCode < 400) {
            return (object)[
                "status" => $statusCode,
                "body" => $body,
            ];
        }

        throw new Error(
            $body->message ?? $body->Message ?? 'Unknown error',
            $body->code ?? '666',
            $statusCode
        );
    }

    /**
     * @throws Error
     */
    public static function post($url, $data = [], $headers = [])
    {
        $curl = curl_init();

        $curlOptions = self::$defaultConfig + [
                CURLOPT_URL => $url,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => self::getHeaders($headers)
            ];
        curl_setopt_array($curl, $curlOptions);

        return self::execute($curl);
    }

    /**
     * @throws Error
     */
    public static function get($url, $data = [], $headers = [])
    {
        $curl = curl_init();

        $curlOptions = self::$defaultConfig + [
                CURLOPT_URL => $url . '?' . http_build_query($data),
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => self::getHeaders($headers)
            ];
        curl_setopt_array($curl, $curlOptions);

        return self::execute($curl);
    }
}
