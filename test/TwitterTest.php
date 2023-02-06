<?php

namespace Noweh\TwitterApi\Test;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Client;
use function PHPUnit\Framework\assertTrue;

class TwitterTest extends TestCase
{
    /** @var Client $client */
    private Client $client;

    /** @var array $settings */
    private static array $settings = [];

    private static int $pageSize = 25;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        // may lead to Exception : Incomplete settings passed.
        if (class_exists('Dotenv')) {
            $dotenv = Dotenv::createUnsafeImmutable(__DIR__.'/config', '.env');
            $dotenv->safeLoad();
        }

        foreach (getenv() as $settingKey => $settingValue) {
            if (strpos($settingKey, 'TWITTER_') === 0) {
                self::$settings[str_replace('twitter_', '', mb_strtolower($settingKey))] = $settingValue;
            }
        }

        $this->client = new Client(self::$settings);
    }

    /**
     * Lookup Tweets by Keyword.
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testSearchTweets(): void
    {
        $response = $this->client->tweetSearch()
            ->addFilterOnKeywordOrPhrase(['php'])
            ->addFilterOnLocales(['fr', 'en'])
            ->addMaxResults(self::$pageSize)
            ->showUserDetails()
            ->showMetrics()
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        assertTrue(property_exists($response, 'meta'));
    }

    /**
     * Lookup an User
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testSearchUsers(): void
    {
        $response = $this->client->userSearch()
            ->findByIdOrUsername('twitterdev', Client::MODES['USERNAME'])
            ->performRequest();

        assertTrue(is_object($response));
    }

    /**
     * Find mentions by user ID.
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testFindMentions(): void
    {
        $response = $this->client->timeline()
            ->findRecentMentioningForUserId('1538300985570885636')
            ->performRequest();

        assertTrue(is_object($response));
    }

    /**
     * Share Tweet
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testTweet(): void
    {
        $date = new \DateTime('NOW');
        $response = $this->client->tweet()
            ->performRequest('POST', [
                'text' => 'BIP BIP BIP... ' . $date->format(\DateTimeInterface::ATOM) .
                    ' Wake up! A new commit is on github (noweh/twitter-api-v2-php)...'

            ]
        );

        assertTrue(is_object($response));
    }

    /**
     * Retweet a random Tweet.
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testRetweet(): void
    {
        $response = $this->client
            ->tweetSearch()
            ->addFilterOnKeywordOrPhrase(['php'])
            ->addFilterOnLocales(['fr', 'en'])
            ->addMaxResults(self::$pageSize)
            ->showUserDetails()
            ->showMetrics()
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        assertTrue(property_exists($response, 'meta'));

        // Retweet by random index
        $tweet_id = $response->data[rand(0, self::$pageSize-1)]->id;
        $response2 = $this->client
            ->retweet()
            ->performRequest(
                'POST', ['tweet_id' => $tweet_id]
            );

        assertTrue(is_object($response2));
    }

    /**
     * Retrieve Tweets by user ID.
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testFetchTweet(): void
    {
        $response = $this->client->tweet()
            ->performRequest('POST', ['ids' => self::$settings['account_id']]);

        assertTrue(is_object($response));
    }

    /**
     * Retrieve the users which you've blocked.
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testUserBlocks(): void
    {
        $response = $this->client->userBlocks()
            ->lookup()
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        assertTrue(property_exists($response, 'meta'));
        self::logUsers($response->data);
    }

    /**
     * Retrieve the users which are following you.
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testUserFollowers(): void
    {
        $response = $this->client->userFollows()
            ->getFollowers()
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        assertTrue(property_exists($response, 'meta'));
        self::logUsers($response->data);
    }

    /**
     * Retrieve the users which you are following.
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testUserFollowing(): void
    {
        $response = $this->client->userFollows()
            ->getFollowing()
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        assertTrue(property_exists($response, 'meta'));
        self::logUsers($response->data);
    }

    /** Log user nodes to console */
    private static function logUsers(array $data): void
    {
        foreach ($data as $item) {
            $user_id = str_pad($item->id, 20, " ",STR_PAD_LEFT);
            echo $user_id." $item->username \"".str_replace("\n", " ", $item->name)."\"\n";
        }
    }
}
