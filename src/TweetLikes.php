<?php

namespace Noweh\TwitterApi;

/**
 * Class Tweet/Likes Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/tweets/likes/api-reference">Likes</a>
 * @author Martin Zeitler
 */
class TweetLikes extends AbstractController
{
    /** @var int $maxResults */
    private int $maxResults;

    /**
     * @param array<int, string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setAuthMode(1);
    }

    /**
     * The maximum number of search results to be returned by a request.
     * A number between 10 and 100.
     * By default, a request response will return 10 results.
     * @param int $number
     * @return $this
     */
    public function addMaxResults(int $number): TweetLikes
    {
        $this->maxResults = $number;
        return $this;
    }

    /**
     * Tweets liked by a user.
     * @param string $user_id
     * @return TweetLikes
     */
    public function getLikedTweets(string $user_id): TweetLikes
    {
        $this->setEndpoint('users/'.$user_id.'/liked_tweets');
        $this->setHttpRequestMethod('GET');
        return $this;
    }

    /**
     * Users who have liked a Tweet.
     * @param string $tweet_id
     * @return TweetLikes
     */
    public function getUsersWhoLiked(string $tweet_id): TweetLikes
    {
        $this->setEndpoint('tweets/' . $tweet_id . '/liking_users');
        $this->setHttpRequestMethod('GET');
        return $this;
    }

    /**
     * Allows a user ID to like a Tweet
     * @return TweetLikes
     */
    public function like(): TweetLikes
    {
        $this->setEndpoint('users/'.$this->account_id.'/likes');
        $this->setHttpRequestMethod('POST');
        return $this;
    }

    /**
     * Allows a user ID to unlike a Tweet
     * @param int $tweet_id
     * @return TweetLikes
     */
    public function unlike(int $tweet_id): TweetLikes
    {
        $this->setEndpoint('users/'.$this->account_id.'/likes/' . $tweet_id);
        $this->setHttpRequestMethod('DELETE');
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string the URL for the request.
     * @throws \Exception
     */
    protected function constructEndpoint(): string
    {
        if (!empty($this->maxResults)) {
            $this->query_string['max_results'] = $this->maxResults;
        }
        if (!is_null($this->next_page_token)) {
            $this->query_string['pagination_token'] = $this->next_page_token;
        }
        $endpoint = parent::constructEndpoint();
        if (count($this->query_string) > 0) {
            $endpoint .= '?' . http_build_query($this->query_string);
        }
        return $endpoint;
    }
}
