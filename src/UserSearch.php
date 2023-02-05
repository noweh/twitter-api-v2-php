<?php

namespace Noweh\TwitterApi;

class UserSearch extends AbstractController
{
    public const MODES = [
        'ID' => 'id',
        'USERNAME' => 'username'
    ];

    /** @var mixed $idOrUsername */
    private $idOrUsername;

    /** @var string $mode */
    protected string $mode = self::MODES['USERNAME'];

    /** @var int $maxResults */
    private int $maxResults;

    /**
     * @param array<int, string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setEndpoint('users');
    }

    /**
     * The maximum number of search results to be returned by a request.
     * A number between 10 and 100.
     * By default, a request response will return 10 results.
     * @param int $number
     * @return $this
     */
    public function addMaxResults(int $number): UserSearch
    {
        $this->maxResults = $number;
        return $this;
    }

    /**
     * returns details about up to 100 users by ID or Username
     * @param mixed $idOrUsername can be an array of items
     * @param string $mode
     * @return UserSearch
     */
    public function findByIdOrUsername($idOrUsername, string $mode = self::MODES['ID'], string|null $next_page_token=null): UserSearch
    {
        $this->idOrUsername = $idOrUsername;
        if (in_array($mode, self::MODES, true)) {
            $this->mode = $mode;
        }
        if ($next_page_token != null) {
            $this->next_page_token = $next_page_token;
        }
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
        if (empty($this->idOrUsername)) {
            $error = new \stdClass();
            $error->message = 'cURL error';
            $error->details = 'An id or username is required';
            throw new \Exception(json_encode($error, JSON_THROW_ON_ERROR), 403);
        }

        $endpoint = parent::constructEndpoint();
        if (is_array($this->idOrUsername)) {
            if ($this->mode === self::MODES['USERNAME']) {
                $endpoint .= '/by?usernames=';
            } else {
                $endpoint .= '?ids=';
            }
            $endpoint .= implode(',', $this->idOrUsername);
            // Pagination
            if (! is_null($this->next_page_token)) {
                $endpoint .= '&pagination_token=' . $this->next_page_token;
            }
        } else {
            if ($this->mode === self::MODES['USERNAME']) {
                $endpoint .= '/by/username';
            }
            $endpoint .= '/' . $this->idOrUsername;

            // Pagination
            if (! is_null($this->next_page_token)) {
                $endpoint .= '?pagination_token=' . $this->next_page_token;
            }
        }

        if (!empty($this->maxResults)) {
            $endpoint .= '&max_results=' . $this->maxResults;
        }

        return $endpoint;
    }
}
