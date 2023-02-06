<?php

namespace Noweh\TwitterApi\Test;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Client;
use function PHPUnit\Framework\assertTrue;

class UserLookupTest extends AbstractTest
{
    /** @var int $idToLookup */
    private static int $idToLookup = 2244994945;

    /** @var array<int> $idToLookup */
    private static array $idsToLookup = [2244994945];

    /** @var string $nameToLookup */
    private static string $nameToLookup = 'twitterdev';

    /** @var array<string> $namesToLookup */
    private static array $namesToLookup = ['twitterdev'];

    /**
     * Lookup a User by username
     * @throws GuzzleException | Exception
     */
    public function testLookupByName(): void
    {
        $response = $this->client->userLookup()
            ->findByIdOrUsername(self::$nameToLookup)
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
        self::logUsers([$response->data]);
    }

    /**
     * Lookup an User by username
     * @throws GuzzleException | Exception
     */
    public function testLookupByNames(): void
    {
        $response = $this->client->userLookup()
            ->findByIdOrUsername(self::$namesToLookup)
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
        self::logUsers($response->data);
    }

    /**
     * Lookup an User by username
     * @throws GuzzleException | Exception
     */
    public function testLookupById(): void
    {
        $response = $this->client->userLookup()
            ->findByIdOrUsername(self::$idToLookup)
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
        self::logUsers([$response->data]);
    }

    /**
     * Lookup an User by username
     * @throws GuzzleException | Exception
     */
    public function testLookupByIds(): void
    {
        $response = $this->client->userLookup()
            ->findByIdOrUsername(self::$idsToLookup)
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
        self::logUsers($response->data);
    }
}
