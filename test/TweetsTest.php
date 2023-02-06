<?php /** @noinspection PhpIllegalPsrClassPathInspection */

namespace Noweh\TwitterApi\Test;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use function PHPUnit\Framework\assertTrue;

class TweetsTest extends AbstractTest
{
    /** @var array<string> $keywordFilter parameter for TweetLookup. */
    private static array $keywordFilter = ['php'];

    /** @var array<string> $localeFilter parameter for TweetLookup. */
    private static array $localeFilter = ['en', 'fr', 'de'];

    /** @var int $userLiked */
    private static int $userLiked = 93711247;

    /**
     * Timeline: Find recent mentions by user ID.
     * @throws GuzzleException | Exception
     */
    public function testTimelineRecentMentions(): void
    {
        $response = $this->client->timeline()
            ->getRecentMentions(self::$settings['account_id'])
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
            ->getRecentTweets(self::$settings['account_id'])
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

    /**
     * Likes: Tweets liked by a user.
     * @throws GuzzleException | Exception
     */
    public function testLikedTweets(): void
    {
        $response = $this->client->tweetLikes()
            ->addMaxResults(self::$pageSize)
            ->getLikedTweets(self::$settings['account_id'])
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
        self::logTweets($response->data);
    }

    /**
     * Likes: Tweets liked by a user.
     * @throws GuzzleException | Exception
     */
    public function testUsersWhoLiked(): void
    {
        $tweet_id = 1093540451678851072;
        $response = $this->client->tweetLikes()
            ->addMaxResults(self::$pageSize)
            ->getUsersWhoLiked($tweet_id)
            ->performRequest();
        assertTrue(is_object($response));
        if (property_exists($response, 'meta') && $response->meta->result_count > 0) {
            assertTrue( property_exists($response, 'data'));
            self::logUsers($response->data);
        } else {
            echo "Nobody ever liked this tweet.";
        }
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

        assertTrue(is_object($response) && property_exists($response, 'data') && property_exists($response, 'meta'));
        self::logTweets($response->data);
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

    /**
     * Bookmarks: Lookup a user's Bookmarks (will fail).
     * @throws GuzzleException | Exception
     */
    public function testBookmarksLookup(): void
    {
        $response = $this->client->tweetBookmarks()
            ->lookup()
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
        self::logTweets($response->data);
    }
}
