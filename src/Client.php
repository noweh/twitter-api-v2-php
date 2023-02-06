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
    public function retweet(): Retweet
    {
        return new Retweet($this->settings);
    }

    /**
     * Access to Timeline endpoints
     * @return Timeline
     * @throws \Exception
     */
    public function timeline(): Timeline
    {
        return new Timeline($this->settings);
    }

    /**
     * Access to Tweet endpoints
     * @return Tweet
     * @throws \Exception
     */
    public function tweet(): Tweet
    {
        return new Tweet($this->settings);
    }

    /**
     * Access to TweetSearch endpoints
     * @return TweetSearch
     * @throws \Exception
     */
    public function tweetSearch(): TweetSearch
    {
        return new TweetSearch($this->settings);
    }

    /**
     * Access To UserSearch endpoints
     * @return UserSearch
     * @throws \Exception
     */
    public function userSearch(): UserSearch
    {
        return new UserSearch($this->settings);
    }

    /**
     * Access To User/Blocks endpoints
     * @return UserBlocks
     * @throws \Exception
     */
    public function userBlocks(): UserBlocks
    {
        return new UserBlocks($this->settings);
    }

    /**
     * Access To User/Follows endpoints
     * @return UserBlocks
     * @throws \Exception
     */
    public function userFollows(): UserFollows
    {
        return new UserFollows($this->settings);
    }

    /**
     * Access To User/Mutes endpoints
     * @return UserMutes
     * @throws \Exception
     */
    public function userMutes(): UserMutes
    {
        return new UserMutes($this->settings);
    }
}
