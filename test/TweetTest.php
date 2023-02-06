<?php

namespace Noweh\TwitterApi\Test;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Client;
use function PHPUnit\Framework\assertTrue;

class TweetTest extends BasicTest
{
    /**
     * Lookup Tweets by Keyword.
     * @throws GuzzleException | Exception
     */
    public function testTweetLookup(): void
    {
        $response = $this->client->tweetLookup()
            ->addFilterOnKeywordOrPhrase(self::$keywordFilter)
            ->addFilterOnLocales(self::$localeFilter)
            ->addMaxResults(self::$pageSize)
            ->showUserDetails()
            ->showMetrics()
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data') && property_exists($response, 'meta'));
        self::logTweets($response->data);
    }

    /**
     * Share Tweet
     * @throws GuzzleException | Exception
     */
    public function testTweet(): void
    {
        $date = new \DateTime('NOW');
        $response = $this->client->tweet()->create()
            ->performRequest([
                'text' => 'Test Tweet... ' . $date->format(\DateTimeInterface::ATOM)
            ]
        );
        assertTrue(is_object($response) && property_exists($response, 'data'));
    }

    /**
     * Retrieve Tweet by tweet ID.
     * @throws GuzzleException | Exception
     */
    public function testFetchTweet(): void
    {
        $response = $this->client->tweet()->fetch(1622477565565739010)
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
        self::logTweets($response->data);
    }
}
