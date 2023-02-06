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

        if (!isset($settings['account_id'])) {
            throw new \Exception('Incomplete settings passed. Expected "account_id"');
        }

        $this->setEndpoint('users/' . $settings['account_id'] . '/retweets');
        $this->setHttpRequestMethod('POST');
        $this->setAuthMode(1);
    }
}
