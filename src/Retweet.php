<?php

namespace Noweh\TwitterApi;

// https://developer.twitter.com/en/docs/twitter-api/tweets/retweets/api-reference
class Retweet extends AbstractController
{
    /**
     * @param array<string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setEndpoint('users/' . $this->account_id . '/retweets');
        $this->setHttpRequestMethod('POST');
        $this->setAuthMode(1);
    }
}
