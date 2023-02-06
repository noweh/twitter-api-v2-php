<?php

namespace Noweh\TwitterApi;

class Tweet extends AbstractController
{
    public const MODES = [
        'FETCH' => 'fetch',
        'CREATE' => 'create',
        'DELETE' => 'delete'
    ];

    private int $target_tweet_id;

    /**
     * @param array<string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);

        if (!isset($settings['account_id'])) {
            throw new \Exception('Incomplete settings passed. Expected "account_id"');
        }

        $this->setEndpoint('tweets');
        $this->setAuthMode(1);
    }

    public function fetch(int $tweet_id): Tweet
    {
        $this->setHttpRequestMethod('GET');
        $this->mode = self::MODES['FETCH'];
        $this->target_tweet_id = $tweet_id;
        return $this;
    }

    /**
     * Create a Tweet.
     * @see https://developer.twitter.com/en/docs/twitter-api/tweets/manage-tweets/api-reference/post-tweets
     * @return Tweet
     */
    public function create(): Tweet
    {
        $this->setHttpRequestMethod('POST');
        $this->mode = self::MODES['CREATE'];
        return $this;
    }

    /**
     * Delete a Tweet.
     * @param int $tweet_id
     * @return Tweet
     */
    public function delete(int $tweet_id): Tweet
    {
        $this->setHttpRequestMethod('DELETE');
        $this->target_tweet_id = $tweet_id;
        $this->mode = self::MODES['DELETE'];
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string the URL for the request.
     * @throws \Exception
     */
    protected function constructEndpoint(): string {
        $endpoint = parent::constructEndpoint();
        if ($this->mode == self::MODES['FETCH']) {
            $endpoint .= '?ids='.$this->target_tweet_id;
        }
        if ($this->mode == self::MODES['DELETE']) {
            $endpoint .= '/'.$this->target_tweet_id;
        }
        return $endpoint;
    }
}
