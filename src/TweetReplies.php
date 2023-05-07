<?php

namespace Noweh\TwitterApi;

/**
 * Class Tweet/Replies Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/tweets/hide-replies/api-reference/put-tweets-id-hidden">Hide replies</a>
 * @author Martin Zeitler
 */
class TweetReplies extends AbstractController
{
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
     * Hides or unhides a reply to a Tweet.
     * The Tweet must belong to a conversation initiated by the authenticating user.
     * @param int $tweet_id Unique identifier of the Tweet to hide or unhide.
     * @return TweetReplies
     */
    public function hideReply(int $tweet_id): TweetReplies
    {
        $this->setEndpoint('tweets/' . $tweet_id . '/hidden');
        $this->setHttpRequestMethod('PUT');
        return $this;
    }
}
