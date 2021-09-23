<?php

namespace Noweh\TwitterApi\Test;

use Noweh\TwitterApi\Retweet;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\TweetSearch;
use Noweh\TwitterApi\UserSearch;

class TwitterTest extends TestCase
{
    /** @var array $settings */
    private $settings = [];

    public function setUp(): void
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__.'/config', '.env');
            $dotenv->load();
        } catch (\Exception $e) {
            throw new \Exception('test/config/.env file does not exists', '403');
        }

        foreach ($_ENV as $settingKey => $settingValue) {
            $this->settings[str_replace('twitter_', '', mb_strtolower($settingKey))] = $settingValue;
        }
    }

    /**
     * Case 1: Search a Tweet
     * @throws \JsonException
     * @throws \Exception
     */
    public function testSearchTweetsOnTwitter(): void
    {
        $this->assertIsObject($this->searchWithParameters());
    }

    /**
     * Case 2: Search an User
     * @throws \JsonException
     * @throws \Exception
     */
    public function testSearchUsersOnTwitter(): void
    {
        $this->assertIsObject(
            (new UserSearch($this->settings))
            ->findByIdOrUsername('twitterdev', UserSearch::MODES['USERNAME'])
            ->performRequest()
        );
    }

    /**
     * Case 3: Retweet a Tweet
     * @throws \JsonException
     */
    public function testRetweetOnTwitter(): void
    {
        $retweeter = new Retweet($this->settings);

        $searchResult = $this->searchWithParameters();
        $this->assertObjectHasAttribute('data', $searchResult);

        if (property_exists($searchResult, 'data')) {
            foreach ($searchResult->data as $tweet) {
                $return = $retweeter->performRequest('POST', ['tweet_id' => $tweet->id]);
                $this->assertIsObject($return);
            }
        }
    }

    /**
     * Return a list of tweets with users details
     * @throws \JsonException
     * @throws \Exception
     */
    private function searchWithParameters(): \stdClass
    {
        return (new TweetSearch($this->settings))
            ->showMetrics()
            ->onlyWithMedias()
            ->addFilterOnKeywordOrPhrase(['twitter'])
            ->addFilterOnLocales(['fr', 'en'])
            ->showUserDetails()
            ->performRequest()
        ;
    }
}
