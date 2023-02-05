<?php

namespace Noweh\TwitterApi\Test;

use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Client;
use function PHPUnit\Framework\assertTrue;

class TwitterTest extends TestCase
{
    /** @var Client $twitterClient */
    private Client $twitterClient;

    private static array $settings = [];

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

        $this->twitterClient = new Client(self::$settings);
    }

    /**
     * Case 1: Search a Tweet
     * @throws \JsonException | GuzzleException
     */
    public function testSearchTweets(): void
    {
        $this->assertIsObject($this->searchWithParameters(['php']));
    }

    /**
     * Case 2: Search an User
     * @throws \JsonException | GuzzleException
     */
    public function testSearchUsers(): void
    {
        $this->assertIsObject(
            $this->twitterClient->userSearch()
            ->findByIdOrUsername('twitterdev', Client::MODES['USERNAME'])
            ->performRequest()
        );
    }

    /**
     * List blocked users
     * @throws GuzzleException | \JsonException | \Exception
     */
    public function testUserBlockList(): void
    {
        $this->assertIsObject(
            $this->twitterClient->userBlock()
                ->lookup('twitterdev')
                ->performRequest()
        );
    }

    /**
     * Case 3: Find mentions
     * @throws \JsonException
     * @throws \Exception|\GuzzleHttp\Exception\GuzzleException
     */
    public function testFindMentions(): void
    {
        $this->assertIsObject(
            $this->twitterClient->timeline()
                ->findRecentMentioningForUserId('1538300985570885636')
                ->performRequest()
        );
    }

    /**
     * Case 4: Tweet
     * @throws \JsonException|\GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function testTweet(): void
    {
        $date = new \DateTime('NOW');

        $return = $this->twitterClient->tweet()->performRequest(
            'POST',
            [
                'text' =>
                    'BIP BIP BIP... ' .
                    $date->format(\DateTimeInterface::ATOM) .
                    ' Wake up!  A new commit is on github (noweh/twitter-api-v2-php)....'

            ]
        );

        $this->assertIsObject($return);
    }

    /**
     * Case 5: Retweet a Tweet
     * @throws \JsonException|\Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testRetweet(): void
    {
        $searchResult = $this->searchWithParameters(['php']);
        if (is_object($searchResult)) {
            assertTrue(property_exists($searchResult, 'data'));
            if (property_exists($searchResult, 'data')) {
                $return = $this->twitterClient->retweet()->performRequest('POST', [
                    'tweet_id' => $searchResult->data[0]->id
                ]);
                $this->assertIsObject($return);
            }
        } else {
            throw new \Exception('error when test', 403);
        }
    }

    /**
     * Case 6: Fetch Tweet by Id
     * @throws \JsonException|\Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testFetchTweet(): void
    {
        $this->assertIsObject($this->twitterClient->tweet()->performRequest(
            'POST', [
                'ids' => self::$settings['account_id']
            ]
        ));
    }

    /**
     * Return a list of tweets with users details
     * @param array<string> $keywords
     * @param array<string> $usernames
     * @param bool $onlyWithMedia
     * @return mixed
     * @throws \JsonException|\Exception|\GuzzleHttp\Exception\GuzzleException
     */
    private function searchWithParameters(array $keywords = [], array $usernames = [], $onlyWithMedia = false): mixed
    {
        $request = $this->twitterClient->tweetSearch()
            ->showMetrics()
            ->addFilterOnLocales(['fr', 'en'])
            ->addMaxResults(11)
            ->showUserDetails()
        ;

        if ($onlyWithMedia) {
            $request->onlyWithMedias();
        }

        if ($keywords) {
            $request->addFilterOnKeywordOrPhrase($keywords);
        }

        if ($usernames) {
            $request->addFilterOnUsernamesFrom($usernames);
        }

        return $request->performRequest();
    }
}
