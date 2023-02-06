<?php

namespace Noweh\TwitterApi\Test;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use function PHPUnit\Framework\assertTrue;

class UseeMutesTest extends AbstractTest
{
    /** @var int $userToMute mute/unmute user ID */
    private static int $userToMute = 2244994945;

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
}
