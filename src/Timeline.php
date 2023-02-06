<?php

namespace Noweh\TwitterApi;

// https://developer.twitter.com/en/docs/twitter-api/tweets/timelines/api-reference

class Timeline extends AbstractController
{
    /**
     * User mention timeline
     * Returns most recent Tweets mentioning a specified user ID
     * @param int $user_id
     * @return Timeline
     */
    public function getRecentMentions(int $user_id): Timeline
    {
        $this->setEndpoint('users/' . $user_id . '/mentions');
        return $this;
    }

    /**
     * User Tweet timeline
     * Returns most recent Tweets composed a specified user ID
     * @param int $user_id
     * @return Timeline
     */
    public function getRecentTweets(int $user_id): Timeline
    {
        $this->setEndpoint('users/' . $user_id . '/tweets');
        return $this;
    }

    /**
     * Reverse chronological timeline
     * Returns a collection of recent Tweets by you and users you follow.
     * @return Timeline
     */
    public function getReverseChronological(): Timeline
    {
        $this->setAuthMode(1);
        $this->setEndpoint('users/' . $this->account_id . '/timelines/reverse_chronological');
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string the URL for the request.
     * @throws \Exception
     */
    protected function constructEndpoint(): string {
        $endpoint = parent::constructEndpoint();
        if (! is_null($this->next_page_token)) {
            $endpoint .= '?pagination_token=' . $this->next_page_token;
        }
        return $endpoint;
    }
}
