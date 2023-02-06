<?php

namespace Noweh\TwitterApi;

/**
 * Class User/Mutes Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/users/mutes/introduction">Mutes</a>
 * @author Martin Zeitler
 */
class UserMutes extends AbstractController {

    public const MODES = [
        'LOOKUP' => 'lookup',
        'UNMUTE' => 'unmute',
        'MUTE' => 'mute'
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
        $this->setEndpoint('users/'.$this->account_id.'/muting');
    }

    /**
     * Look up muted users.
     * @return UserMutes
     */
    public function lookup(): UserMutes
    {
        $this->mode = self::MODES['LOOKUP'];
        return $this;
    }

    /**
     * Mute user by username or ID.
     * @return UserMutes
     */
    public function mute(): UserMutes
    {
        $this->setHttpRequestMethod('POST');
        $this->mode = self::MODES['MUTE'];
        return $this;
    }

    /**
     * Mute user by username or ID.
     * @param int $user_id
     * @return UserMutes
     */
    public function unmute(int $user_id): UserMutes
    {
        $this->setHttpRequestMethod('DELETE');
        $this->mode = self::MODES['UNMUTE'];
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
        if ($this->mode == self::MODES['UNMUTE']) {
            $endpoint .= '/'.$this->target_user_id;
        }

        // Pagination
        if (! is_null($this->next_page_token)) {
            $endpoint .= '?pagination_token=' . $this->next_page_token;
        }

        return $endpoint;
    }
}
