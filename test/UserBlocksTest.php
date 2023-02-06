<?php

namespace Noweh\TwitterApi\Test;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Client;
use function PHPUnit\Framework\assertTrue;

class UserBlocksTest extends BasicTest
{
    /** @var int $userToBlock block/unblock user ID */
    private static int $userToBlock = 2244994945;

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
}
