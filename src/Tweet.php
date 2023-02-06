<?php

namespace Noweh\TwitterApi;

class Tweet extends AbstractController
{
    /**
     * @param array<string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        if (! property_exists($this, 'account_id')) {
            throw new \Exception('Incomplete settings passed. Expected "account_id"');
        }
        $this->setAuthMode(1);
    }

    public function fetch(int $tweet_id): Tweet
    {
        $this->setHttpRequestMethod('GET');
        $this->setEndpoint('tweets?ids=' . $tweet_id);
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
        $this->setEndpoint('tweets');
        return $this;
    }

    /**
     * Delete a Tweet.
     * @param int $tweet_id
     * @return Tweet
     */
    public function delete(int $tweet_id): Tweet
    {
        $this->setEndpoint('tweets/' . $tweet_id);
        $this->setHttpRequestMethod('DELETE');
        return $this;
    }
}
