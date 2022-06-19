<?php

namespace Noweh\TwitterApi\Test;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Client;

class TwitterTest extends TestCase
{
    /** @var Client $twitterClient */
    private Client $twitterClient;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $dotenv = Dotenv::createUnsafeImmutable(__DIR__.'/config', '.env');
        $dotenv->safeLoad();

        $settings = [];
        foreach (getenv() as $settingKey => $settingValue) {
            if (strpos($settingKey, 'TWITTER_') === 0) {
                $settings[str_replace('twitter_', '', mb_strtolower($settingKey))] = $settingValue;
            }
        }

        $this->twitterClient = new Client($settings);
    }

    /**
     * Case 1: Search a Tweet
     * @throws \JsonException
     * @throws \Exception|\GuzzleHttp\Exception\GuzzleException
     */
    public function testSearchTweets(): void
    {
        $this->assertIsObject($this->searchWithParameters(['php']));
    }

    /**
     * Case 2: Search an User
     * @throws \JsonException
     * @throws \Exception|\GuzzleHttp\Exception\GuzzleException
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
            $this->assertObjectHasAttribute('data', $searchResult);

            if (property_exists($searchResult, 'data')) {
                $return = $this->twitterClient->retweet()
                    ->performRequest('POST', ['tweet_id' => $searchResult->data[0]->id]);
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
            'GET',
            [
                'id' => '1455953449422516226'
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
    private function searchWithParameters(array $keywords = [], array $usernames = [], $onlyWithMedia = false)
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
