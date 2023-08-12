<?php

namespace Noweh\TwitterApi;

/**
 * Class User/Lookup Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/users/lookup/api-reference">Users Lookup</a>
 * @author Julien Schmitt
 */
class UserLookup extends AbstractController
{
    public const MODES = [
        'ID' => 'id',
        'USERNAME' => 'username'
    ];

    /** @var ?string $mode mode of operation */
    protected ?string $mode = null;

    /** @var int|string|array<string|int> $idOrUsername */
    private $idOrUsername;

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
     * Returns details about up to 100 users by ID or Username
     * @param int|string|array<string|int> $idOrUsername
     * @return UserLookup
     */
    public function findByIdOrUsername($idOrUsername): UserLookup
    {
        $this->idOrUsername = $idOrUsername;
        if (is_int($this->idOrUsername) || (is_array($this->idOrUsername) && is_numeric($this->idOrUsername[0]))) {
            $this->mode = self::MODES['ID'];
        } else if (is_string($this->idOrUsername) || is_string($this->idOrUsername[0])) {
            $this->mode = self::MODES['USERNAME'];
        }
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string the URL for the request.
     * @throws \JsonException | \Exception
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
            if (!is_null($this->next_page_token)) {
                $endpoint .= '&pagination_token=' . $this->next_page_token;
            }
        } else {
            if ($this->mode === self::MODES['USERNAME']) {
                $endpoint .= '/by/username';
            }
            $endpoint .= '/' . $this->idOrUsername;

            // Pagination
            if (!is_null($this->next_page_token)) {
                $endpoint .= '?pagination_token=' . $this->next_page_token;
            }
        }
        if (preg_match('/\?/', $endpoint)) {
            $endpoint .= '&user.fields=description';
        } else {
            $endpoint .= '?user.fields=description';
        }

        return $endpoint;
    }
}
