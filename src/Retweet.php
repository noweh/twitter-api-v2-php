<?php

namespace Noweh\TwitterApi;

/**
 * Class Retweet Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/tweets/retweets/api-reference">Tweet Retweet</a>
 * @author Julien Schmitt
 */
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
