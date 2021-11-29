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
    private string $mode = self::MODES['USERNAME'];

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
     * returns details about up to 100 users by ID or Username
     * @param mixed $idOrUsername can be an array of items
     * @param string $mode
     * @return UserSearch
     */
    public function findByIdOrUsername($idOrUsername, string $mode = self::MODES['ID']): UserSearch
    {
        $this->idOrUsername = $idOrUsername;
        if (in_array($mode, self::MODES, true)) {
            $this->mode = $mode;
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
        } else {
            if ($this->mode === self::MODES['USERNAME']) {
                $endpoint .= '/by/username';
            }
            $endpoint .= '/' . $this->idOrUsername;
        }

        return $endpoint;
    }
}
