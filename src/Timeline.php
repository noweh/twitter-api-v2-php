<?php

namespace Noweh\TwitterApi;

class Timeline extends AbstractController
{
    /**
     * @param string $userId
     * @return Timeline
     */
    public function findRecentMentionsForUserId(string $userId): Timeline
    {
        $this->setEndpoint('users/' . $userId . '/mentions');

        return $this;
    }

    /**
     * @param string $userId
     * @return Timeline
     */
    public function findRecentTweetsByUserId(string $userId): Timeline
    {
        $this->setEndpoint('users/' . $userId . '/tweets');
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string the URL for the request.
     * @throws \Exception
     */
    protected function constructEndpoint(): string {
        $endpoint = parent::constructEndpoint();

        // Pagination
        if (! is_null($this->next_page_token)) {
            $endpoint .= '?pagination_token=' . $this->next_page_token;
        }
        return $endpoint;
    }
}
