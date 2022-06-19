<?php

namespace Noweh\TwitterApi;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\HandlerStack;

abstract class AbstractController
{
    /** @const string API_URL */
    private const API_BASE_URI = 'https://api.twitter.com/2/';

    /**
     * @var string
     */
    private $access_token;

    /**
     * @var string
     */
    private $access_token_secret;

    /**
     * @var string
     */
    private $consumer_key;

    /**
     * @var string
     */
    private $consumer_secret;

    /**
     * @var string
     */
    private $bearer_token;

    /**
     * @var string $endpoint
     */
    private $endpoint;

    /**
     * Creates object. Requires an array of settings.
     * @param array<string> $settings
     * @throws Exception when CURL extension is not loaded
     */
    public function __construct(array $settings = [])
    {
        if (!extension_loaded('curl')) {
            throw new Exception('PHP extension CURL is not loaded.');
        }

        if (!isset(
            $settings['access_token'],
            $settings['access_token_secret'],
            $settings['consumer_key'],
            $settings['consumer_secret'],
            $settings['bearer_token']
        )) {
            throw new Exception('Incomplete settings passed.');
        }

        $this->access_token = $settings['access_token'];
        $this->access_token_secret = $settings['access_token_secret'];
        $this->consumer_key = $settings['consumer_key'];
        $this->consumer_secret = $settings['consumer_secret'];
        $this->bearer_token = $settings['bearer_token'];
    }

    /**
     * Perform the request to Twitter API
     * @param string $method
     * @param array<string, mixed> $postData
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     * @throws Exception
     */
    public function performRequest(string $method = 'GET', array $postData = [])
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
            if ($method === 'GET') {
                // Inject the Bearer token into the header for the call
                $client = new Client(['base_uri' => self::API_BASE_URI]);

                $headers['Authorization'] = 'Bearer ' . $this->bearer_token;

                // if GET method with id set, fetch tweet with id
                if (is_array($postData) && isset($postData['id']) && is_numeric($postData['id'])) {
                    $this->endpoint .= '/'.$postData['id'];
                    // unset to avoid clash later.
                    unset($postData['id']);
                }
            } else {
                // Inject Oauth handler
                $stack = HandlerStack::create();
                $middleware = new Oauth1([
                    'consumer_key' => $this->consumer_key,
                    'consumer_secret' => $this->consumer_secret,
                    'token' => $this->access_token,
                    'token_secret' => $this->access_token_secret,
                ]);
                $stack->push($middleware);

                $client = new Client([
                    'base_uri' => self::API_BASE_URI,
                    'handler' => $stack,
                    'auth' => 'oauth'
                ]);
            }

            $response  = $client->request($method, $this->constructEndpoint(), [
                'headers' => $headers,
                // this is always array from function spec,use count to see if data set.
                // Otherwise twitter error on empty data.
                'json' => count($postData) ? $postData: null,
            ]);

            $body = json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);

            if ($response->getStatusCode() >= 400) {
                $error = new \stdClass();
                $error->message = 'cURL error';
                if ($body) {
                    $error->details = $response;
                }
                throw new Exception(
                    json_encode($error, JSON_THROW_ON_ERROR),
                    $response->getStatusCode()
                );
            }

            return $body;
        } catch (ClientException | ServerException $e) {
            throw new Exception(json_encode($e->getResponse()->getBody()->getContents(), JSON_THROW_ON_ERROR));
        }
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
