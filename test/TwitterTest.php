<?php

namespace Noweh\TwitterApi\Test;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\TwitterSearch;

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
     * Case 1: Search on Twitter
     * @throws \JsonException
     */
    public function testSearchOnTwitter()
    {
        $twitterReturns = (new TwitterSearch($_ENV['TWITTER_API_BEARER_TOKEN']))
            ->showMetrics()
            ->onlyWithMedias()
            ->addFilterOnUsernamesFrom([
                'twitterdev',
                'Noweh95'
            ], TwitterSearch::OPERATORS['OR'])
            ->addFilterOnKeywordOrPhrase([
                'Dune',
                'DenisVilleneuve'
            ], TwitterSearch::OPERATORS['AND'])
            ->addFilterOnLocales(['fr', 'en'])
            ->showUserDetails()
            ->performRequest()
        ;

        $this->assertIsObject($twitterReturns);
    }
}
