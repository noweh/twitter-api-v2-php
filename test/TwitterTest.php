<?php

namespace Noweh\TwitterApi\Test;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\TweetSearch;
use Noweh\TwitterApi\UserSearch;

class TwitterTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/config', '.env');
            $dotenv->load();
        } catch (\Exception $e) {
            throw new \Exception('test/config/.env file does not exists', '403');
        }

        parent::__construct($name, $data, $dataName);
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
        $authenticatedUser = (new UserSearch($_ENV['TWITTER_API_BEARER_TOKEN']))
            ->findByIdOrUsername($_ENV['TWITTER_ID'])
            ->performRequest()
        ;

        $this->assertIsObject($authenticatedUser);
    }

    /**
     * Return a list of tweets with users details
     * @throws \JsonException
     * @throws \Exception
     */
    private function searchWithParameters(): \stdClass
    {
        return (new TweetSearch($_ENV['TWITTER_API_BEARER_TOKEN']))
            ->showMetrics()
            ->onlyWithMedias()
            ->addFilterOnUsernamesFrom(['twitterdev'], TweetSearch::OPERATORS['OR'])
            ->addFilterOnLocales(['fr', 'en'])
            ->showUserDetails()
            ->performRequest()
        ;
    }
}
