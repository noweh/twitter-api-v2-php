<?php

namespace Noweh\TwitterApi;

/**
 * Class User/Follows Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/users/follows/api-reference">Follows</a>
 * @author Martin Zeitler
 */
class UserFollows extends AbstractController {

    /**
     * @param array<int, string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setEndpoint('users/'.$this->account_id);
        $this->setAuthMode(1);
    }

    /**
     * Followers of user ID.
     * @return UserFollows
     */
    public function getFollowers(): UserFollows
    {
        $this->setEndpoint('users/'.$this->account_id.'/followers');
        return $this;
    }

    /**
     * Users a user ID is following.
     * @return UserFollows
     */
    public function getFollowing(): UserFollows
    {
        $this->setEndpoint('users/'.$this->account_id.'/following');
        return $this;
    }

    /**
     * Follow a user ID
     * @return UserFollows
     */
    public function follow(): UserFollows
    {
        $this->setHttpRequestMethod('POST');
        $this->setEndpoint('users/'.$this->account_id.'/following');
        return $this;
    }

    /**
     * Unfollow a user ID
     * @param int $target_user_id
     * @return UserFollows
     */
    public function unfollow(int $target_user_id): UserFollows
    {
        $this->setHttpRequestMethod('DELETE');
        $this->setEndpoint('users/'.$this->account_id.'/following/'.$target_user_id);
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
            $this->query_string['pagination_token'] = $this->next_page_token;
            $endpoint .= '?' . http_build_query($this->query_string);
        }
        return $endpoint;
    }
}
