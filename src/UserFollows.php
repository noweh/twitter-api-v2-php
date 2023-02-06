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

    private mixed $idOrUsername;

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
        $this->setAuthMode(1);
        $this->mode = self::MODES['FOLLOWERS'];
        return $this;
    }

    /**
     * Users a user ID is following.
     * @return UserFollows
     */
    public function getFollowing(): UserFollows
    {
        $this->setAuthMode(1);
        $this->mode = self::MODES['FOLLOWING'];
        return $this;
    }

    /**
     * Follow a user ID
     * @param mixed $user_id
     * @return UserFollows
     */
    public function follow(mixed $user_id): UserFollows
    {
        $this->setAuthMode(1);
        $this->idOrUsername = $user_id;
        $this->mode = self::MODES['FOLLOW'];
        return $this;
    }

    /**
     * Follow a user ID
     * @param mixed $user_id
     * @return UserFollows
     */
    public function unfollow(mixed $user_id): UserFollows
    {
        $this->setAuthMode(1);
        $this->idOrUsername = $user_id;
        $this->mode = self::MODES['UNFOLLOW'];
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string
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
                $endpoint .= '/following/'.$this->idOrUsername;
                break;
        }

        // Pagination
        if (! is_null($this->next_page_token)) {
            $endpoint .= '?pagination_token=' . $this->next_page_token;
        }

        return $endpoint;
    }
}