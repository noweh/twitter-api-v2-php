<?php

namespace Noweh\TwitterApi\Test;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Client;
use function PHPUnit\Framework\assertTrue;

class UserFollowsTest extends AbstractTest
{
    /** @var int $userToFollow follow/unfollow user ID */
    private static int $userToFollow = 2244994945;

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
     * Follow a user.
     * @throws GuzzleException | Exception
     */
    public function testUserFollow(): void
    {
        $response = $this->client->userFollows()->follow()
            ->performRequest(['target_user_id' => self::$userToFollow]);

        assertTrue(is_object($response) && property_exists($response, 'data'));
    }

    /**
     * Unfollow a user.
     * @throws GuzzleException | Exception
     */
    public function testUserUnfollow(): void
    {
        $response = $this->client->userFollows()->unfollow(self::$userToFollow)
            ->performRequest();

        assertTrue(is_object($response) && property_exists($response, 'data'));
    }

}
