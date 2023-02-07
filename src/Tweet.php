<?php

namespace Noweh\TwitterApi;

/**
 * Class Tweet Controller
 * @author Julien Schmitt
 */
class Tweet extends AbstractController
{
    /**
     * @param array<string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setAuthMode(1);
    }

    public function fetch(int $tweet_id): Tweet
    {
        $this->setEndpoint('tweets?ids=' . $tweet_id);
        $this->setHttpRequestMethod('GET');
        return $this;
    }

    /**
     * Create a Tweet.
     * @see https://developer.twitter.com/en/docs/twitter-api/tweets/manage-tweets/api-reference/post-tweets
     * @return Tweet
     */
    public function create(): Tweet
    {
        $this->setEndpoint('tweets');
        $this->setHttpRequestMethod('POST');
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
