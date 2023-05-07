<?php

namespace Noweh\TwitterApi;

/**
 * Class Tweet/Quotes Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/tweets/quote-tweets/api-reference/get-tweets-id-quote_tweets">Quote Tweets</a>
 * @author Martin Zeitler
 */
class TweetQuotes extends AbstractController
{
    /**
     * @param array<int, string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setAuthMode(1);
    }

    /**
     * Returns Quote Tweets for a Tweet specified by the requested Tweet ID.
     * @param int $tweet_id Unique identifier of the Tweet to request.
     * @return TweetQuotes
     */
    public function getQuoteTweets(int $tweet_id): TweetQuotes
    {
        $this->setEndpoint('tweets/' . $tweet_id . '/quote_tweets');
        return $this;
    }
}
