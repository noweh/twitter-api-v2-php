<?php

namespace Noweh\TwitterApi;

use Exception;

abstract class AbstractController
{
    /** @const string API_URL */
    private const API_URL = 'https://api.twitter.com/2';

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

    /** @var string $endpoint */
    private $endpoint;

    /**
     * Creates object. Requires an array of settings.
     * @param array $settings
     * @throws Exception when CURL extension is not loaded
     */
    public function __construct(array $settings)
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
     * @param null $postData
     * @return \stdClass
     * @throws \JsonException
     * @throws Exception
     */
    public function performRequest(string $method = 'GET', $postData = null): \stdClass
    {
        $ch = curl_init(self::API_URL . $this->constructEndpoint());

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            $this->buildAuthorizationHeader($method, $postData),
            'Expect:'
        ]);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($postData) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData, JSON_THROW_ON_ERROR));
        }

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

    /**
     * Generate authorization header (Bearer or Oauth) string
     * @param $method
     * @param null $postData
     * @return string
     */
    private function buildAuthorizationHeader($method, $postData = null): string
    {
        if ($method === 'GET') {
            // Inject the Bearer token into the header
            $return = 'Authorization: Bearer ' . $this->bearer_token;
        } else {
            // Use Oauth1.0a to inject into the header
            $return = 'Authorization: OAuth ';

            $values = [];
            foreach ($this->buildOauth($method, $postData) as $key => $value) {
                if (in_array($key, array('oauth_consumer_key', 'oauth_nonce', 'oauth_signature',
                    'oauth_signature_method', 'oauth_timestamp', 'oauth_token', 'oauth_version'))) {
                    $values[] = "$key=\"" . rawurlencode($value) . "\"";
                }
            }

            $return .= implode(', ', $values);
        }

        return $return;
    }

    /**
     * Build Oauth data
     * @param $method
     * @param null $postData
     * @return array
     */
    private function buildOauth($method, $postData = null): array
    {
        $oauth = array(
            'oauth_consumer_key' => $this->consumer_key,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $this->access_token,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );

        if ($postData) {
            foreach ($postData as $key => $value) {
                $oauth[$key] = $value;
            }
        }

        ksort($oauth);

        $rawData = [];
        foreach ($oauth as $key => $value) {
            $rawData[] = rawurlencode($key) . '=' . rawurlencode($value);
        }

        $oauthSignature = base64_encode(
            hash_hmac(
                'sha1',
                $method . '&' . rawurlencode(self::API_URL . $this->constructEndpoint()) . '&' . rawurlencode(implode('&', $rawData)),
                rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->access_token_secret),
                true
            )
        );

        $oauth['oauth_signature'] = $oauthSignature;

        return $oauth;
    }
}
