<?php /** @noinspection PhpIllegalPsrClassPathInspection */

namespace Noweh\TwitterApi\Test;

use Exception;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Noweh\TwitterApi\Client;

abstract class AbstractTest extends TestCase
{
    /** @var Client $client */
    protected Client $client;

    /** @var array $settings */
    private static array $settings = [];

    /** @var array $keywordFilter parameter for TweetLookup. */
    protected static array $keywordFilter = ['php'];

    /** @var array $localeFilter parameter for TweetLookup. */
    protected static array $localeFilter = ['en', 'fr', 'de'];

    /** @var int $pageSize */
    protected static int $pageSize = 10;

    /**
     * Set up Test Case
     * @throws Exception
     */
    public function setUp(): void
    {
        // Error : Class "Dotenv\Dotenv" not found.
        if (class_exists('Dotenv')) {
            $dotenv = Dotenv::createUnsafeImmutable(__DIR__.'/config', '.env');
            $dotenv->safeLoad();
        }

        // Initialize from environmental variables.
        foreach (getenv() as $key => $value) {
            if (str_starts_with($key, 'TWITTER_')) {
                $name = str_replace('twitter_', '', mb_strtolower($key));
                self::$settings[$name] = $value;
            }
        }

        $this->client = new Client(self::$settings);
    }

    /** Log tweet nodes to console */
    protected static function logTweets(array|\stdClass $data): void
    {
        if (is_object($data)) {
            // Tweet
            $tweet_id = str_pad($data->id, 20, " ",STR_PAD_LEFT);
            echo "$tweet_id \"".str_replace("\n", " ", $data->text)."\"\n";
        } else {
            foreach ($data as $item) {
                $tweet_id = str_pad($item->id, 20, " ",STR_PAD_LEFT);
                if (property_exists($item, 'author_id')) {
                    $user_id = str_pad($item->author_id, 20, " ",STR_PAD_LEFT);
                    echo $user_id." $tweet_id \"".str_replace("\n", " ", $item->text)."\"\n";
                } else {
                    // Mentions
                    echo "$tweet_id \"".str_replace("\n", " ", $item->text)."\"\n";
                }
            }
        }
    }

    /** Log user nodes to console */
    protected static function logUsers(array $data): void
    {
        foreach ($data as $item) {
            $user_id = str_pad($item->id, 20, " ",STR_PAD_LEFT);
            echo $user_id." $item->username \"".str_replace("\n", " ", $item->name)."\"\n";
        }
    }
}
