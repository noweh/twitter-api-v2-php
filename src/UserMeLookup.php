<?php

namespace Noweh\TwitterApi;

/**
 * Class User/Lookup Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/users/lookup/api-reference">Users Lookup</a>
 * @author Julien Schmitt
 */

class UserMeLookup extends AbstractController
{
    /**
     * @param array<int, string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->setEndpoint('users/me');
        $this->setAuthMode(1);
    }
    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string the URL for the request.
     * @throws \JsonException | \Exception
     */
    protected function constructEndpoint(): string
    {
        if ($this->free_mode) {
            return parent::constructEndpoint() .
                '?user.fields=created_at,description,entities,id,location,name,most_recent_tweet_id,' .
                'profile_image_url,protected,public_metrics,url,username,verified,verified_type' .
                '&expansions=pinned_tweet_id'
            ;
        }

        return parent::constructEndpoint() .
            '?user.fields=created_at,description,entities,id,location,name,most_recent_tweet_id,' .
            'profile_image_url,protected,public_metrics,url,username,verified,verified_type,withheld' .
            '&tweet.fields=attachments,author_id,context_annotations,conversation_id,created_at,edit_controls,' .
            'edit_history_tweet_ids,entities,geo,id,in_reply_to_user_id,lang,non_public_metrics,note_tweet,' .
            'organic_metrics,possibly_sensitive,public_metrics,referenced_tweets,reply_settings,source,text,withheld' .
            '&expansions=pinned_tweet_id'
        ;
    }
}
