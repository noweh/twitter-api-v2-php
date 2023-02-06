<?php

namespace Noweh\TwitterApi;

/**
 * Class User/Mutes Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/users/mutes/introduction">Mutes</a>
 * @author Martin Zeitler
 */
class UserMutes extends AbstractController {

    private int $target_user_id;

    /**
     * @param array<int, string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        if (! property_exists($this, 'account_id')) {
            throw new \Exception('Incomplete settings passed. Expected "account_id"');
        }
        $this->setAuthMode(1);
    }

    /**
     * Look up muted users.
     * @return UserMutes
     */
    public function lookup(): UserMutes
    {
        $this->setEndpoint('users/'.$this->account_id.'/muting');
        return $this;
    }

    /**
     * Mute user by username or ID.
     * @return UserMutes
     */
    public function mute(): UserMutes
    {
        $this->setEndpoint('users/'.$this->account_id.'/muting');
        $this->setHttpRequestMethod('POST');
        return $this;
    }

    /**
     * Mute user by username or ID.
     * @param int $user_id
     * @return UserMutes
     */
    public function unmute(int $user_id): UserMutes
    {
        $this->$this->setEndpoint('users/'.$this->account_id.'/muting/'.$user_id);
        $this->setHttpRequestMethod('DELETE');
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
