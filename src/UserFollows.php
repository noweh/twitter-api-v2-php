<?php

namespace Noweh\TwitterApi;

/**
 * Class User/Follows Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/users/follows/introduction">Follows</a>
 * @author Martin Zeitler
 */
class UserFollows extends AbstractController {

    public const MODES = [
        'FOLLOWERS' => 'followers',
        'FOLLOWING' => 'following',
        'UNFOLLOW' => 'unfollow',
        'FOLLOW' => 'follow'
    ];

    private int $target_user_id;

    /**
     * @param array<int, string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        if (!isset($settings['account_id'])) {
            throw new \Exception('Incomplete settings passed. Expected "account_id"');
        }

        $this->setAuthMode(1);
        $this->setEndpoint('users/'.$this->account_id);
    }

    /**
     * Followers of user ID.
     * @return UserFollows
     */
    public function getFollowers(): UserFollows
    {
        $this->mode = self::MODES['FOLLOWERS'];
        return $this;
    }

    /**
     * Users a user ID is following.
     * @return UserFollows
     */
    public function getFollowing(): UserFollows
    {
        $this->mode = self::MODES['FOLLOWING'];
        return $this;
    }

    /**
     * Follow a user ID
     * @return UserFollows
     */
    public function follow(): UserFollows
    {
        $this->setHttpRequestMethod('POST');
        $this->mode = self::MODES['FOLLOW'];
        return $this;
    }

    /**
     * Unfollow a user ID
     * @param int $user_id
     * @return UserFollows
     */
    public function unfollow(int $user_id): UserFollows
    {
        $this->setHttpRequestMethod('DELETE');
        $this->mode = self::MODES['UNFOLLOW'];
        $this->target_user_id = $user_id;
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string the URL for the request.
     * @throws \Exception
     */
    protected function constructEndpoint(): string {
        $endpoint = parent::constructEndpoint();
        switch ($this->mode) {
            case self::MODES['FOLLOWERS']:
                $endpoint .= '/followers';
                break;
            case self::MODES['FOLLOW']:
            case self::MODES['FOLLOWING']:
                $endpoint .= '/following';
                break;
            case self::MODES['UNFOLLOW']:
                $endpoint .= '/following/'.$this->target_user_id;
                break;
        }

        // Pagination
        if (! is_null($this->next_page_token)) {
            $endpoint .= '?pagination_token=' . $this->next_page_token;
        }

        return $endpoint;
    }
}
