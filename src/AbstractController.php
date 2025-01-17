<?php
namespace Noweh\TwitterApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\HandlerStack;

abstract class AbstractController
{
    /** @const API_BASE_URI */
    private const API_BASE_URI = 'https://api.twitter.com/2/';

    /**
     * @const API_METHODS
     * TODO: The HTTP method could be defined from within child controllers,
     *       in order not having to pass the method with ->performRequest().
     */
    protected const API_METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

    /**
     * @var int $auth_mode API Auth Mode
     *                     0 use Bearer token.
     *                     1 use OAuth1 token.
     *                     2 use Authorization Code Flow
     */
    protected int $auth_mode = 0;

    /** @var string $endpoint */
    private string $endpoint = '';

    /** @var int $account_id OAuth1 User ID */
    protected int $account_id;

    /** @var string */
    private string $access_token;

    /** @var string */
    private string $access_token_secret;

    /** @var string */
    private string $consumer_key;

    /** @var string */
    private string $consumer_secret;

    /** @var string */
    private string $bearer_token;

    /** @var bool */
    protected bool $free_mode = false;

    /** @var string|null $next_page_token Next Page Token for API pagination. */
    protected ?string $next_page_token = null;

    /** @var string $mode mode of operation */
    private string $http_request_method = 'GET';

    /** @var array<string|int> $query_string */
    protected array $query_string = [];

    /** @var array<string> $post_body */
    protected array $post_body = [];

    /** @var string> $baseUri */
    private string $api_base_uri;

    /**
     * Creates object. Requires an array of settings.
     * @param array<string> $settings
     * @throws \Exception when CURL extension is not loaded
     */
    public function __construct(array $settings)
    {
        $this->extensionLoaded('curl');
        $this->extensionLoaded('json');
        $this->parseSettings($settings);
    }

    private function getAPIBaseURI(): string
    {
        return $this->api_base_uri;
    }
    

    /**
     * Perform the request to Twitter API
     * @param array<string, mixed> $postData
     * @return \stdClass|null
     * @throws GuzzleException|\RuntimeException|\JsonException
     */
    public function performRequest(array $postData = [], bool $withHeaders = false): ?\stdClass
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            if ($this->auth_mode === 0) { // Bearer Token
                // Inject the Bearer token header
                $client = new Client(['base_uri' => $this->getAPIBaseURI()]);
                $headers['Authorization'] = 'Bearer ' . $this->bearer_token;
            } elseif ($this->auth_mode === 1) { // OAuth 1.0a User Context
                // Insert Oauth1 middleware
                $stack = HandlerStack::create();
                $middleware = new Oauth1([
                    'consumer_key' => $this->consumer_key,
                    'consumer_secret' => $this->consumer_secret,
                    'token' => $this->access_token,
                    'token_secret' => $this->access_token_secret,
                ]);
                $stack->push($middleware);
                $client = new Client([
                    'base_uri' => $this->getAPIBaseURI(),
                    'handler' => $stack,
                    'auth' => 'oauth'
                ]);
            } else { // OAuth 2.0 Authorization Code Flow
                throw new \RuntimeException('OAuth 2.0 Authorization Code Flow had not been implemented & also requires user interaction.');
            }

            $response  = $client->request($this->getHttpRequestMethod(), $this->constructEndpoint(), [
                'verify' => !$this->is_windows(), // else composer script will break.
                'headers' => $headers,
                // This is always array from function spec, use count to see if data set.
                // Otherwise, twitter error on empty data.
                'json' => count($postData) ? $postData : null,
            ]);

            /** @var \stdClass|null $body */
            $body = json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);

            if ($withHeaders && $body) {
                $body->headers = $response->getHeaders();
            }

            if ($response->getStatusCode() >= 400) {
                $error = new \stdClass();
                $error->message = 'cURL error';
                if ($body) {
                    $error->details = $response;
                }
                throw new \RuntimeException(
                    json_encode($error, JSON_THROW_ON_ERROR),
                    $response->getStatusCode()
                );
            }
            return $body;

        } catch (ServerException $e) {
            /** @var \stdClass|null $payload */
            $payload = json_decode($e->getResponse()->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
            throw new \RuntimeException($payload->detail ?? $e->getMessage(), $payload->status ?? $e->getCode());
        } catch (RequestException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    private function is_windows(): bool
    {
        return DIRECTORY_SEPARATOR === '\\';
    }

    /**
     * Set Auth-Mode
     * @param int $value 0 use Bearer token.
     *                   1 use OAuth1 token.
     *                   2 not implemented.
     * @return void
     */
    public function setAuthMode(int $value): void
    {
        $this->auth_mode = $value;
    }

    /**
     * @throws \Exception
     */
    private function extensionLoaded(string $ext): void
    {
        if (!extension_loaded($ext)) {
            throw new \Exception('PHP extension '.strtoupper($ext).' is not loaded.');
        }
    }

    /**
     * @param array<string> $settings
     * @return void
     * @throws \Exception
     */
    private function parseSettings(array $settings): void
    {
        if (!isset(
            // Account ID
            $settings['account_id'],

            // Consumer Keys
            $settings['consumer_key'],
            $settings['consumer_secret'],

            // Authentication Tokens
            $settings['bearer_token'],
            $settings['access_token'],
            $settings['access_token_secret']
        )) {
            throw new \Exception('Incomplete settings passed.');
        }

        $this->account_id = (int) $settings['account_id'];
        $this->consumer_key = $settings['consumer_key'];
        $this->consumer_secret = $settings['consumer_secret'];
        $this->bearer_token = $settings['bearer_token'];
        $this->access_token = $settings['access_token'];
        $this->access_token_secret = $settings['access_token_secret'];
        $this->free_mode = (bool) ($settings['free_mode'] ?? false);
        $this->api_base_uri = $settings['api_base_uri'] ?? self::API_BASE_URI;
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

    /**
     * Set Pagination Token
     * @param string $value
     * @return AbstractController
     * @noinspection PhpUnused
     */
    public function setPaginationToken(string $value): AbstractController
    {
        $this->next_page_token = $value;
        return $this;
    }

    /**
     * Set HTTP Request Method
     * @param string $value
     * @return void
     */
    protected function setHttpRequestMethod(string $value): void
    {
        if (in_array($value, self::API_METHODS)) {
            $this->http_request_method = $value;
        }
    }

    /**
     * Get HTTP Request Method
     * @return string
     */
    private function getHttpRequestMethod(): string
    {
        return $this->http_request_method;
    }
}
