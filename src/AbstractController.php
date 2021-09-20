<?php

namespace Noweh\TwitterApi;

use Exception;

abstract class AbstractController
{
    /** @const string API_URL */
    private const API_URL = 'https://api.twitter.com/2';

    /** @var string $bearer */
    private $bearer;

    /** @var string $endpoint */
    private $endpoint;

    /**
     * Creates object using bearer.
     * @param string $bearer
     * @throws Exception when CURL extension is not loaded
     */
    public function __construct(string $bearer)
    {
        if (!extension_loaded('curl')) {
            throw new Exception('PHP extension CURL is not loaded.');
        }

        $this->bearer = $bearer;
    }

    /**
     * Perform the request to Twitter API
     * @return \stdClass
     * @throws \JsonException
     * @throws Exception
     */
    public function performRequest(): \stdClass
    {
        $ch = curl_init(self::API_URL . $this->constructEndpoint());
        $authorization = "Authorization: Bearer " . $this->bearer;
        // Inject the token into the header
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json' , $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseBody = json_decode(curl_exec($ch), false, 512, JSON_THROW_ON_ERROR); // Execute the cURL statement
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($responseCode >= 400) {
            $error = new \stdClass();
            $error->message = 'cURL error';
            if ($responseBody) {
                $error->details = $responseBody;
            } elseif ($errno = curl_errno($ch)) {
                $error->details = '(' . $errno . '): ' . curl_strerror($errno);
            }
            curl_close($ch); // Close the cURL connection
            throw new Exception(
                json_encode($error, JSON_THROW_ON_ERROR),
                $responseCode
            );
        }
        curl_close($ch);

        return $responseBody;
    }

    /**
     * Set Endpoint value
     * @param string $endpoint
     * @return void
     */
    protected function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Retrieve Endpoint value
     * @return string
     */
    protected function constructEndpoint(): string
    {
        return $this->endpoint;
    }
}