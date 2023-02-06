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

    /** @var array $keywordFilter parameter for TweetLookup. */
    private static array $keywordFilter = ['php'];

    /** @var array $localeFilter parameter for TweetLookup. */
    private static array $localeFilter = ['en', 'fr', 'de'];

    /** @var int $pageSize parameter for TweetLookup. */
    private static int $pageSize = 10;

    /** @var int $userToFollow follow/unfollow user ID */
    private static int $userToFollow = 44196397;

    /** @var int $userToBlock block/unblock user ID */
    private static int $userToBlock = 44196397;

    /** @var int $userToMute mute/unmute user ID */
    private static int $userToMute = 44196397;

    /** @var int $userMentioned mentioned user ID. */
    private static int $userMentioned = 1538300985570885636;

    /**
     * Set up Test Case
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
     * Timeline: Find recent mentions by user ID.
     * @throws GuzzleException | Exception
     */
    public function testTimelineRecentMentions(): void
    {
        $response = $this->client->timeline()
            ->getRecentMentions(self::$userMentioned)
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
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

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        self::logTweets($response->data);
    }

    /**
     * Timeline: Reverse Chronological Timeline by user ID.
     * @throws GuzzleException | Exception
     */
    public function testTimelineReverseChronological(): void
    {
        $response = $this->client->timeline()
            ->getReverseChronological((int) self::$settings['account_id'])
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        self::logTweets($response->data);
    }

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

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        assertTrue(property_exists($response, 'meta'));
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
        $response = $this->client->tweetLookup()
            ->addMaxResults(self::$pageSize)
            ->addFilterOnKeywordOrPhrase(self::$keywordFilter)
            ->addFilterOnLocales(self::$localeFilter)
            ->showUserDetails()
            ->showMetrics()
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        assertTrue(property_exists($response, 'meta'));
        self::logTweets($response->data);

        // Retweet by random index
        $tweet_id = $response->data[rand(0, self::$pageSize-1)]->id;
        $response2 = $this->client->retweet()
            ->performRequest(['tweet_id' => $tweet_id]);

        assertTrue(is_object($response2));
    }

    /**
     * Retrieve Tweet by tweet ID.
     * @throws GuzzleException | Exception
     */
    public function testFetchTweet(): void
    {
        $response = $this->client->tweet()->fetch(1622477565565739010)
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        self::logTweets($response->data);
    }

    /**
     * Lookup an User
     * @throws GuzzleException | Exception
     */
    public function testUserLookup(): void
    {
        $response = $this->client->userLookup()
            ->findByIdOrUsername('twitterdev', Client::MODES['USERNAME'])
            ->performRequest();

        assertTrue(is_object($response));
        assertTrue(property_exists($response, 'data'));
        self::logUsers([$response->data]);
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
     * @throws GuzzleException | Exception
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
     * @throws GuzzleException | Exception
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
     * @throws GuzzleException | Exception
     */
    public function testUserFollow(): void
    {
        $response = $this->client->userFollows()->follow()
            ->performRequest(['target_user_id' => self::$userToFollow]);

        assertTrue(is_object($response));
    }

    /**
     * Unfollow a user.
     * @throws GuzzleException | Exception
     */
    public function testUserUnfollow(): void
    {
        $response = $this->client->userFollows()->unfollow(self::$userToFollow)
            ->performRequest();

        assertTrue(is_object($response));
    }

    /** Log tweet nodes to console */
    private static function logTweets(array|\stdClass $data): void
    {
        if (is_object($data)) {
            // Tweet
            $tweet_id = str_pad($data->id, 20, " ",STR_PAD_LEFT);
            echo "$data->id \"".str_replace("\n", " ", $data->text)."\"\n";
        } else {
            foreach ($data as $item) {
                $tweet_id = str_pad($item->id, 20, " ",STR_PAD_LEFT);
                if (property_exists($item, 'author_id')) {
                    $user_id = str_pad($item->author_id, 20, " ",STR_PAD_LEFT);
                    echo $user_id." $tweet_id \"".str_replace("\n", " ", $item->text)."\"\n";
                } else {
                    // Mentions
                    echo "$tweet_id \"".str_replace("\n", " ", $item->text)."\"\n";
                }
            }
        }
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
