<?php

namespace Noweh\TwitterApi;

class TweetSearch extends AbstractController
{
    public const OPERATORS = [
        'OR' => 'OR',
        'AND' => ''
    ];

    /** @var array<string> $filteredUsernamesFrom */
    private array $filteredUsernamesFrom = [];

    /** @var string $operatorOnFilteredUsernamesFrom */
    private string $operatorOnFilteredUsernamesFrom = self::OPERATORS['OR'];

        /** @var array<string> $filteredUsernamesTo */
    private array $filteredUsernamesTo = [];

    /** @var string $operatorOnFilteredUsernamesTo */
    private string $operatorOnFilteredUsernamesTo = self::OPERATORS['OR'];

    /** @var array<string> $filteredKeywords */
    private array $filteredKeywords = [];

    /** @var string $operatorOnFilteredKeywords */
    private string $operatorOnFilteredKeywords = self::OPERATORS['OR'];

    /** @var string $filteredConversationId */
    private string $filteredConversationId;

    /** @var array<string> $filteredLocales */
    private array $filteredLocales = [];

    /** @var bool $addMetrics */
    private bool $addMetrics = false;

    /** @var bool $addUserDetails */
    private bool $addUserDetails = false;

    /** @var bool $hasMedias */
    private bool $hasMedias = false;

    /** @var int $maxResults */
    private int $maxResults;

    /**
     * @param array<string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setEndpoint('tweets/search/recent');
    }

    /**
     * Matches any Tweet from a specific user.
     * The value can be either the username (excluding the @ character) or the user’s numeric user ID.
     * @param array<string> $usernames
     * @param string|null $operator
     * @return TweetSearch
     */
    public function addFilterOnUsernamesFrom(array $usernames, string $operator = null): TweetSearch
    {
        $this->filteredUsernamesFrom = $usernames;
        if (in_array($operator, self::OPERATORS, true)) {
            $this->operatorOnFilteredUsernamesFrom = $operator;
        }

        return $this;
    }

    /**
     * Matches any Tweet that is in reply to a particular user.
     * The value can be either the username (excluding the @ character) or the user’s numeric user ID.
     * @param array<string> $usernames
     * @param string|null $operator
     * @return TweetSearch
     */
    public function addFilterOnUsernamesTo(array $usernames, string $operator = null): TweetSearch
    {
        $this->filteredUsernamesTo = $usernames;
        if (in_array($operator, self::OPERATORS, true)) {
            $this->operatorOnFilteredUsernamesTo = $operator;
        }

        return $this;
    }

    /**
     * Matches the exact phrase or a hashtag within the body of a Tweet.
     * @param array<string> $keywords
     * @param string|null $operator
     * @return TweetSearch
     */
    public function addFilterOnKeywordOrPhrase(array $keywords, string $operator = null): TweetSearch
    {
        $this->filteredKeywords = $keywords;
        if (in_array($operator, self::OPERATORS, true)) {
            $this->operatorOnFilteredKeywords = $operator;
        }

        return $this;
    }

    /**
     * Matches any Tweet that is in reply to a particular conversation ID.
     * The value can be either the username (excluding the @ character) or the user’s numeric user ID.
     * @param string $conversationId
     * @return TweetSearch
     */
    public function addFilterOnConversationId(string $conversationId): TweetSearch
    {
        $this->filteredConversationId = $conversationId;

        return $this;
    }

    /**
     * Matches Tweets that have been classified by Twitter as being of a particular language
     * (if, and only if, the Tweet has been classified).
     * It is important to note that each Tweet is currently only classified as being of one language,
     * so AND’ing together multiple languages will yield no results.
     * @param array<string> $locales
     * @return TweetSearch
     */
    public function addFilterOnLocales(array $locales): TweetSearch
    {
        $this->filteredLocales = $locales;
        return $this;
    }

    /**
     * The maximum number of search results to be returned by a request.
     * A number between 10 and 100.
     * By default, a request response will return 10 results.
     * @param int $number
     * @return $this
     */
    public function addMaxResults(int $number): TweetSearch
    {
        $this->maxResults = $number;
        return $this;
    }

    /**
     * Show Metrics in response
     * @return TweetSearch
     */
    public function showMetrics(): TweetSearch
    {
        $this->addMetrics = true;
        return $this;
    }

    /**
     * Show UserDetails in response
     * @return TweetSearch
     */
    public function showUserDetails(): TweetSearch
    {
        $this->addUserDetails = true;
        return $this;
    }

    /**
     * Matches Tweets that contain a media object, such as a photo, GIF, or video, as determined by Twitter.
     * This will not match on media created with Periscope, or Tweets with links to other media hosting sites.
     * @return TweetSearch
     */
    public function onlyWithMedias(): TweetSearch
    {
        $this->hasMedias = true;
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string
     * @throws \JsonException
     * @throws \Exception
     */
    protected function constructEndpoint(): string
    {
        $endpoint = parent::constructEndpoint();

        if (empty($this->filteredKeywords) &&
            empty($this->filteredUsernamesFrom) &&
            empty($this->filteredUsernamesTo) &&
            empty($this->filteredConversationId)
        ) {
            $error = new \stdClass();
            $error->message = 'cURL error';
            $error->details = 'A filter on keyword or user is required';

            throw new \Exception(json_encode($error, JSON_THROW_ON_ERROR), 403);
        }

        $endpoint .= '?query=';

        if (!empty($this->filteredKeywords)) {
            $loop = 0;
            $endpoint .= '(';
            foreach ($this->filteredKeywords as $keyword) {
                ++$loop;
                $qtyKeywords = count($this->filteredKeywords);

                $endpoint .= '("' . $keyword . '"%20OR%20%23' . $keyword . ')';
                if ($qtyKeywords > 1 && $loop < $qtyKeywords) {
                    $endpoint .= '%20' . $this->operatorOnFilteredKeywords . '%20';
                }
            }
            $endpoint .= ')';
        }

        if (!empty($this->filteredUsernamesFrom)) {
            $endpoint .= '%20(from:' .
                implode('%20' . $this->operatorOnFilteredUsernamesFrom . '%20from:', $this->filteredUsernamesFrom) . ')';
        }

        if (!empty($this->filteredUsernamesTo)) {
            $endpoint .= '%20(to:' .
                implode('%20' . $this->operatorOnFilteredUsernamesTo . '%20to:', $this->filteredUsernamesTo) . ')';
        }

        if (!empty($this->filteredConversationId)) {
            $endpoint .= '%20conversation_id:' . $this->filteredConversationId;
        }

        if (!empty($this->filteredLocales)) {
            $endpoint .= '%20(lang:' . implode('%20OR%20lang:', $this->filteredLocales) . ')';
        }

        if ($this->hasMedias) {
            $endpoint .= '%20has:media';
        }

        if (!empty($this->maxResults)) {
            $endpoint .= '&max_results=' . $this->maxResults;
        }

        if ($this->addMetrics) {
            $endpoint .= '&tweet.fields=public_metrics';
        }

        $endpoint .= '&expansions=attachments.media_keys';

        if ($this->addUserDetails) {
            $endpoint .= ',author_id&user.fields=description';
        }

        $endpoint .= '&media.fields=duration_ms,height,media_key,preview_image_url,public_metrics,type,url,width,alt_text';

        return $endpoint;
    }
}
