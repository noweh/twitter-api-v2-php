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
    /** @const string API_BASE_URI */
    private const API_BASE_URI = 'https://api.twitter.com/2/';

    /**
     * @var int $auth_mode API Auth Mode
     *                     0 use Bearer token.
     *                     1 use OAuth1 token.
     */
    protected int $auth_mode = 0;

    /**
     * @var int $account_id OAuth1 User ID
     */
    protected int $account_id;

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
    private $endpoint = '';

    /** @var string $next_page_token Next Page Token for API pagination. */
    protected string $next_page_token;

    /** @var string $mode */
    protected string $mode;

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
            // Consumer Keys
            $settings['consumer_key'],
            $settings['consumer_secret'],

            // Authentication Tokens
            $settings['bearer_token'],
            $settings['account_id'],
            $settings['access_token'],
            $settings['access_token_secret']
        )) {
            throw new Exception('Incomplete settings passed.');
        }

        $this->consumer_key = $settings['consumer_key'];
        $this->consumer_secret = $settings['consumer_secret'];
        $this->bearer_token = $settings['bearer_token'];
        $this->account_id = $settings['account_id']; // TWITTER_ACCOUNT_ID; also contained in TWITTER_ACCESS_TOKEN.
        $this->access_token = $settings['access_token'];
        $this->access_token_secret = $settings['access_token_secret'];
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
    public function performRequest(string $method = 'GET', array $postData = []): mixed
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            if ($this->auth_mode == 0) {

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
                // This is always array from function spec, use count to see if data set.
                // Otherwise, twitter error on empty data.
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
            $payload = str_replace("\n", "", $e->getResponse()->getBody()->getContents());
            throw new Exception($payload);
        }
    }

    /**
     * Set Auth-Mode value
     *
     * @param int $value 0 use Bearer token.
     *                   1 use OAuth1 token.
     * @return void
     */
    public function setAuthMode(int $value): void
    {
        $this->auth_mode = $value;
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
