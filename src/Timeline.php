<?php

namespace Noweh\TwitterApi;

class Timeline extends AbstractController
{
    /**
     * @param string $userId
     * @return Timeline
     */
    public function findRecentMentioningForUserId(string $userId): Timeline
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
}