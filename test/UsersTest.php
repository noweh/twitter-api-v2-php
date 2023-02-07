<?php /** @noinspection PhpIllegalPsrClassPathInspection */

namespace Noweh\TwitterApi\Test;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use function PHPUnit\Framework\assertTrue;

class UsersTest extends BaseTestCase
{
    /** int $userToBlock block/unblock user ID */
    //private static int $userToBlock = 2244994945;

    /** @var int $userToFollow follow/unfollow user ID */
    private static int $userToFollow = 2244994945;

    /** @var int $idToLookup */
    private static int $idToLookup = 2244994945;

    /** @var array<int> $idsToLookup */
    private static array $idsToLookup = [93711247, 2244994945];

    /** @var string $nameToLookup */
    private static string $nameToLookup = 'twitterdev';

    /** @var array<string> $namesToLookup */
    private static array $namesToLookup = ['androiddev', 'twitterdev'];

    /** int $userToMute mute/unmute user ID */
    //private static int $userToMute = 2244994945;

    /**
     * Retrieve the users which you've blocked.
     * @throws GuzzleException | Exception
     */
    public function testUserBlocks(): void
    {
        $response = $this->client->userBlocks()->lookup()
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data') && property_exists($response, 'meta'));
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

        assertTrue(is_object($response) && property_exists($response, 'data') && property_exists($response, 'meta'));
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

        assertTrue(is_object($response) && property_exists($response, 'data') && property_exists($response, 'meta'));
        self::logUsers($response->data);
    }

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
     * Lookup a User by username
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

    /**
     * Retrieve the users which you've muted.
     * @throws GuzzleException | Exception
     */
    public function testUserMutes(): void
    {
        $response = $this->client->userMutes()->lookup()
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data') && property_exists($response, 'meta'));
        self::logUsers($response->data);
    }

    /**
     * Follow a user.
     * @throws GuzzleException | Exception
     */
    public function testUserFollow(): void
    {
        try {
            $response = $this->client->userFollows()->follow()
                ->performRequest(['target_user_id' => self::$userToFollow]);
            assertTrue(is_object($response) && property_exists($response, 'data'));
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->markTestSkipped('Test skipped: ' . $e->getMessage());
        }
    }

    /**
     * Unfollow a user.
     * @throws GuzzleException | Exception
     */
    public function testUserUnfollow(): void
    {
        try {
            $response = $this->client->userFollows()->unfollow(self::$userToFollow)
                ->performRequest(['target_user_id' => self::$userToFollow]);
            assertTrue(is_object($response) && property_exists($response, 'data'));
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->markTestSkipped('Test skipped: ' . $e->getMessage());
        }
    }

}
