<?php

namespace Noweh\TwitterApi;

/**
 * Class Timeline Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/tweets/timelines/api-reference">Tweet Timelines</a>
 * @author Julien Schmitt
 */
class Timeline extends AbstractController
{
    /**
     * User mention timeline
     * Returns most recent Tweets mentioning a specified user ID
     * @param string $user_id
     * @return Timeline
     */
    public function getRecentMentions(string $user_id): Timeline
    {
        $this->setEndpoint('users/' . $user_id . '/mentions');
        return $this;
    }

    /**
     * User Tweet timeline
     * Returns most recent Tweets composed a specified user ID
     * @param string $user_id
     * @return Timeline
     */
    public function getRecentTweets(string $user_id): Timeline
    {
        $this->setEndpoint('users/' . $user_id . '/tweets');
        return $this;
    }

    /**
     * Reverse chronological timeline
     * Returns a collection of recent Tweets by you and users you follow.
     * @return Timeline
     * @throws \Exception
     */
    public function getReverseChronological(): Timeline
    {
        if (property_exists($this, 'account_id')) {
            $this->setEndpoint('users/' . $this->account_id . '/timelines/reverse_chronological');
        } else {
            throw new \Exception('Incomplete settings passed. Expected "account_id"');
        }
        $this->setAuthMode(1);
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string the URL for the request.
     * @throws \Exception
     */
    protected function constructEndpoint(): string {
        $endpoint = parent::constructEndpoint();
        if (!is_null($this->next_page_token)) {
            $this->query_string['pagination_token'] = $this->next_page_token;
            $endpoint .= '?' . http_build_query($this->query_string);
        }
        return $endpoint;
    }
}
