<?php

namespace Noweh\TwitterApi;

class Client
{
    /**
     * Twitter settings.
     *
     * @var array<string>
     */
    protected array $settings = [];

    public const OPERATORS = [
        'OR' => 'OR',
        'AND' => ''
    ];

    public const MODES = [
        'ID' => 'id',
        'USERNAME' => 'username'
    ];

    /**
     * Client initialization
     * @param array<string> $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Access to Retweet endpoints
     * @return Retweet
     * @throws \Exception
     */
    public function retweet()
    {
        return new Retweet($this->settings);
    }

    /**
     * Access to Timeline endpoints
     * @return Timeline
     * @throws \Exception
     */
    public function timeline()
    {
        return new Timeline($this->settings);
    }

    /**
     * Access to Tweet endpoints
     * @return Tweet
     * @throws \Exception
     */
    public function tweet()
    {
        return new Tweet($this->settings);
    }

    /**
     * Access to TweetSearch endpoints
     * @return TweetSearch
     * @throws \Exception
     */
    public function tweetSearch()
    {
        return new TweetSearch($this->settings);
    }

    /**
     * Access To UserSearch endpoints
     * @return UserSearch
     * @throws \Exception
     */
    public function userSearch()
    {
        return new UserSearch($this->settings);
    }
}
