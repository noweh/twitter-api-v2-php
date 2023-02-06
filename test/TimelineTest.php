<?php

namespace Noweh\TwitterApi\Test;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Client;
use function PHPUnit\Framework\assertTrue;

class TimelineTest extends AbstractTest
{
    /** @var int $userMentioned mentioned user ID. */
    private static int $userMentioned = 1538300985570885636;

    /**
     * Timeline: Find recent mentions by user ID.
     * @throws GuzzleException | Exception
     */
    public function testTimelineRecentMentions(): void
    {
        $response = $this->client->timeline()
            ->getRecentMentions(self::$userMentioned)
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
        self::logTweets($response->data);
    }

    /**
     * Timeline: Find recent tweets by user ID.
     * @throws GuzzleException | Exception
     */
    public function testTimelineRecentTweets(): void
    {
        $response = $this->client->timeline()
            ->getRecentTweets(self::$userMentioned)
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
        self::logTweets($response->data);
    }

    /**
     * Timeline: Reverse Chronological Timeline by user ID.
     * @throws GuzzleException | Exception
     */
    public function testTimelineReverseChronological(): void
    {
        $response = $this->client->timeline()
            ->getReverseChronological()
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
        self::logTweets($response->data);
    }
}
