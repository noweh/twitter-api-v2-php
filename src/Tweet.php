<?php

namespace Noweh\TwitterApi;

/**
 * Class Tweet Controller
 * @author Julien Schmitt
 */
class Tweet extends AbstractController
{
    const AVAILABLE_FIELDS = [
        'attachments' , 'author_id', 'card_uri', 'context_annotations', 'conversation_id', 'created_at', 'edit_controls',
        'edit_history_tweet_ids', 'entities', 'geo', 'id', 'in_reply_to_user_id', 'lang', 'non_public_metrics',
        'note_tweet', 'organic_metrics', 'possibly_sensitive', 'promoted_metrics', 'public_metrics', 'referenced_tweets',
        'reply_settings', 'source', 'text', 'withheld'
    ];

    /**
     * @param array<string> $settings
     * @throws \Exception
     */

    /**
     * @var string $fieldsForFetch
     */
    private string $fieldsForFetch = '';

    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setAuthMode(1);
    }

    public function fetch(int $tweet_id): Tweet
    {
        $this->setEndpoint('tweets?ids=' . $tweet_id . $this->fieldsForFetch);
        $this->setHttpRequestMethod('GET');
        return $this;
    }

    /**
     * @param array $fields
     * @return Tweet
     * @throws \Exception
     */
    public function addFieldsForFetch(array $fields): Tweet
    {
        if ($this->fieldsForFetch === '') {
            $this->fieldsForFetch = '&tweet.fields=';
        }

        foreach ($fields as $field) {
            if (!in_array($field, self::AVAILABLE_FIELDS)) {
                throw new \Exception('Invalid field name.');
            }

            $this->fieldsForFetch .= $field . ',';
        }

        $this->fieldsForFetch = substr_replace($this->fieldsForFetch, '', -1);

        return $this;
    }

    /**
     * Create a Tweet.
     * @see https://developer.twitter.com/en/docs/twitter-api/tweets/manage-tweets/api-reference/post-tweets
     * @return Tweet
     */
    public function create(): Tweet
    {
        $this->setEndpoint('tweets');
        $this->setHttpRequestMethod('POST');
        return $this;
    }

    /**
     * Delete a Tweet.
     * @param int $tweet_id
     * @return Tweet
     */
    public function delete(int $tweet_id): Tweet
    {
        $this->setEndpoint('tweets/' . $tweet_id);
        $this->setHttpRequestMethod('DELETE');
        return $this;
    }
}
