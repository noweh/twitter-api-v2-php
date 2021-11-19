<?php

namespace Noweh\TwitterApi\Test;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Tweet;
use Noweh\TwitterApi\Retweet;
use Noweh\TwitterApi\TweetSearch;
use Noweh\TwitterApi\UserSearch;

class TwitterTest extends TestCase
{
    /** @var array $settings */
    private $settings = [];

    /**
     * @throws \Exception
     */
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
     * @throws \Exception|\GuzzleHttp\Exception\GuzzleException
     */
    public function testSearchTweetsOnTwitter(): void
    {
        $this->assertIsObject($this->searchWithParameters(['avengers']));
    }

    /**
     * Case 2: Search an User
     * @throws \JsonException
     * @throws \Exception|\GuzzleHttp\Exception\GuzzleException
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
     * Case 3: Tweet
     * @throws \JsonException
     */
    public function testTweetOnTwitter(): void
    {
        $tweet = new Tweet($this->settings);

        $return = $tweet->performRequest('POST', ['text' => 'BIP BIP BIP... This is a test.... ' . mt_rand()]);

        $this->assertIsObject($return);
    }

    /**
     * Case 4: Retweet a Tweet
     * @throws \JsonException
     */
    public function testRetweetOnTwitter(): void
    {
        $retweeter = new Retweet($this->settings);

        $searchResult = $this->searchWithParameters(['avengers']);
        $this->assertObjectHasAttribute('data', $searchResult);

        if (property_exists($searchResult, 'data')) {
            $return = $retweeter->performRequest('POST', ['tweet_id' => $searchResult->data[0]->id]);
            $this->assertIsObject($return);
        }
    }

    /**
     * Return a list of tweets with users details
     * @param array $keywords
     * @param array $usernames
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function searchWithParameters(array $keywords = [], array $usernames = [], $onlyWithMedia = false): \stdClass
    {
        $request = (new TweetSearch($this->settings))
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
