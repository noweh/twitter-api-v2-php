<?php

namespace Noweh\TwitterApi;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use JsonException;

/**
 * Class Media Controller
 * @author Victor Angelier <vangelier@hotmail.com>
 */
class Media extends AbstractController
{
    /**
     * Guzzle HTTP client
     * @var Client
     */
    private Client $client;

    /**
     * @param array<string> $settings
     * @throws Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setAuthMode(1);
        $this->setHttpRequestMethod('POST');
        $this->prepareRequest($settings);
    }

    /**
     * Prepare request to upload images to Twitter
     * @param array<string, mixed> $settings
     * @return void
     */
    private function prepareRequest(array $settings = []): void
    {
        // Insert Oauth1 middleware
        $stack = HandlerStack::create();
        $oAuth1 = new Oauth1([
            'consumer_key' => $settings['consumer_key'],
            'consumer_secret' => $settings['consumer_secret'],
            'token' => $settings['access_token'],
            'token_secret' => $settings['access_token_secret'],
        ]);
        $stack->push($oAuth1);
        $this->client = new Client([
            'base_uri' => "https://upload.twitter.com/1.1/",
            'handler' => $stack,
            'auth' => 'oauth'
        ]);
    }

    /**
     * Upload media to Twitter
     * @param string $filedata Base64 encoded binary file
     * @return array<string, mixed>|null
     * @throws GuzzleException|\RuntimeException|\JsonException
     */
    public function upload(string $filedata = ""): ?array
    {
        try {
            $headers = [
                'Accept' => 'application/json'
            ];

            $response = $this->client->request("POST", "media/upload.json?media_category=TWEET_IMAGE", [
                'verify' => !(DIRECTORY_SEPARATOR === '\\'),
                'headers' => $headers,
                'multipart' => [
                    [
                        "name" => "media_data",
                        "contents" => $filedata
                    ]
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                /** @var array<string, mixed>|null $payload */
                $payload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
                return $payload;
            }
        } catch (ServerException $e) {
            /** @var \stdClass|null $payload */
            $payload = json_decode($e->getResponse()->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            throw new \RuntimeException($payload->detail ?? $e->getMessage(), $payload->status ?? $e->getCode());
        } catch (RequestException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }

        return null;
    }
}
