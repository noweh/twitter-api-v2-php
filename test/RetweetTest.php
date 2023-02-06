<?php

namespace Noweh\TwitterApi\Test;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Client;
use function PHPUnit\Framework\assertTrue;

class RetweetTest extends AbstractTest
{
    /**
     * Retweet a random Tweet.
     * @throws GuzzleException | Exception
     */
    public function testRetweet(): void
    {
        $response = $this->client->tweetLookup()
            ->addMaxResults(self::$pageSize)
            ->addFilterOnKeywordOrPhrase(self::$keywordFilter)
            ->addFilterOnLocales(self::$localeFilter)
            ->showUserDetails()
            ->showMetrics()
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data') && property_exists($response, 'meta'));
        self::logTweets($response->data);

        // Retweet by random index
        $tweet_id = $response->data[rand(0, self::$pageSize-1)]->id;
        $response2 = $this->client->retweet()
            ->performRequest(['tweet_id' => $tweet_id]);

        assertTrue(is_object($response2) && property_exists($response2, 'data'));
    }
}
