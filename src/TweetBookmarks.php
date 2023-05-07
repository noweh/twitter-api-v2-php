<?php

namespace Noweh\TwitterApi;

/**
 * Class Tweet/Bookmarks Controller
 * Note: This endpoint only permits the OAuth 2.0 Authorization Code Flow with PKCE.
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/tweets/bookmarks/api-reference">Bookmarks</a>
 * @author Martin Zeitler
 */
class TweetBookmarks extends AbstractController
{
    /**
     * @param array<int, string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setAuthMode(2);
    }

    /**
     * Lookup a user's Bookmarks.
     * @return TweetBookmarks
     */
    public function lookup(): TweetBookmarks
    {
        $this->setEndpoint('users/' . $this->account_id . '/bookmarks');
        return $this;
    }

    /**
     * Bookmark a Tweet.
     * @return TweetBookmarks
     */
    public function bookmarkTweet(): TweetBookmarks
    {
        $this->setHttpRequestMethod('POST');
        $this->setEndpoint('users/' . $this->account_id . '/bookmarks');
        return $this;
    }

    /**
     * Remove a Bookmark of a Tweet.
     * @param int $target_tweet_id
     * @return TweetBookmarks
     */
    public function removeBookmark(int $target_tweet_id): TweetBookmarks
    {
        $this->setHttpRequestMethod('DELETE');
        $this->setEndpoint('users/' . $this->account_id . '/bookmarks/' . $target_tweet_id);
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string the URL for the request.
     * @throws \Exception
     */
    protected function constructEndpoint(): string
    {
        $endpoint = parent::constructEndpoint();
        if (!is_null($this->next_page_token)) {
            $this->query_string['pagination_token'] = $this->next_page_token;
            $endpoint .= '?' . http_build_query($this->query_string);
        }
        return $endpoint;
    }
}
