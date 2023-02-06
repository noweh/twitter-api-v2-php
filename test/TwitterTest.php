<?php

namespace Noweh\TwitterApi\Test;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
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

    /** @var array $keywordFilter parameter for TweetSearch. */
    private static array $keywordFilter = ['php'];

    /** @var array $localeFilter parameter for TweetSearch. */
    private static array $localeFilter = ['en', 'fr', 'de'];

    /** @var int $pageSize parameter for TweetSearch. */
    private static int $pageSize = 25;

    /** @var int $userToFollow follow/unfollow Mr. Elon Musk. */
    private static int $userToFollow = 44196397;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        // Error : Class "Dotenv\Dotenv" not found.
        if (class_exists('Dotenv')) {
            $dotenv = Dotenv::createUnsafeImmutable(__DIR__.'/config', '.env');
            $dotenv->safeLoad();
        }

        // Initialize from environmental variables.
        foreach (getenv() as $key => $value) {
            if (str_starts_with($key, 'TWITTER_')) {
                $name = str_replace('twitter_', '', mb_strtolower($key));
                self::$settings[$name] = $value;
            }
        }

        $this->client = new Client(self::$settings);
    }

    /**
     * Lookup Tweets by Keyword.
     * @throws GuzzleException | Exception
     */
    public function testSearchTweets(): void
    {
        $response = $this->client->tweetSearch()
            ->addFilterOnKeywordOrPhrase(self::$keywordFilter)
            ->addFilterOnLocales(self::$localeFilter)
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
     * @throws GuzzleException | Exception
     */
    public function testSearchUsers(): void
    {
        $response = $this->client->userSearch()
            ->findByIdOrUsername('twitterdev', Client::MODES['USERNAME'])
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        self::logUsers([$response->data]);
    }

    /**
     * Find mentions by user ID.
     * @throws GuzzleException | Exception
     */
    public function testFindMentions(): void
    {
        $response = $this->client->timeline()
            ->findRecentMentionsForUserId('1538300985570885636')
            ->performRequest();

        assertTrue(is_object($response));
    }

    /**
     * Share Tweet
     * @throws GuzzleException | Exception
     */
    public function testTweet(): void
    {
        $date = new \DateTime('NOW');
        $response = $this->client->tweet()
            ->performRequest([
                'text' => 'BIP BIP BIP... ' . $date->format(\DateTimeInterface::ATOM) .
                    ' Wake up! A new commit is on github (noweh/twitter-api-v2-php)...'

            ]
        );

        assertTrue(is_object($response));
    }

    /**
     * Retweet a random Tweet.
     * @throws GuzzleException | Exception
     */
    public function testRetweet(): void
    {
        $response = $this->client->tweetSearch()
            ->addFilterOnKeywordOrPhrase(self::$keywordFilter)
            ->addFilterOnLocales(self::$localeFilter)
            ->addMaxResults(self::$pageSize)
            ->showUserDetails()
            ->showMetrics()
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        assertTrue(property_exists($response, 'meta'));

        // Retweet by random index
        $tweet_id = $response->data[rand(0, self::$pageSize-1)]->id;
        $response2 = $this->client->retweet()
            ->performRequest(['tweet_id' => $tweet_id]);

        assertTrue(is_object($response2));
    }

    /**
     * Retrieve Tweets by user ID.
     * @throws GuzzleException | Exception
     */
    public function testFetchTweet(): void
    {
        $response = $this->client->timeline()
            ->performRequest(['ids' => self::$settings['account_id']]);

        assertTrue(is_object($response));
    }

    /**
     * Retrieve the users which you've blocked.
     * @throws GuzzleException | Exception
     */
    public function testUserBlocks(): void
    {
        $response = $this->client->userBlocks()->lookup()
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        assertTrue(property_exists($response, 'meta'));
        self::logUsers($response->data);
    }

    /**
     * Retrieve the users which you've muted.
     * @throws GuzzleException | Exception
     */
    public function testUserMutes(): void
    {
        $response = $this->client->userMutes()->lookup()
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
        $response = $this->client->userFollows()->getFollowers()
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
        $response = $this->client->userFollows()->getFollowing()
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        assertTrue(property_exists($response, 'meta'));
        self::logUsers($response->data);
    }

    /**
     * Follow a user.
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testUserFollow(): void
    {
        $response = $this->client->userFollows()->follow()
            ->performRequest(['target_user_id' => self::$userToFollow]);

        assertTrue(is_object($response));
    }

    /**
     * Unfollow a user.
     * @throws \GuzzleHttp\Exception\GuzzleException | \Exception
     */
    public function testUserUnfollow(): void
    {
        $response = $this->client->userFollows()->unfollow(self::$userToFollow)
            ->performRequest();

        assertTrue(is_object($response));
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
