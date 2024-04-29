# Twitter API V2 for PHP

![PHP](https://img.shields.io/badge/PHP-v7.4+-828cb7.svg?style=flat-square&logo=php)
[![Badge Twitter](https://img.shields.io/badge/Twitter%20API-v2-828cb7.svg?style=flat-square&logo=twitter&color=1DA1F2)](https://developer.twitter.com/en/docs/twitter-api)
[![Run Tests](https://github.com/noweh/twitter-api-v2-php/actions/workflows/run-tests.yml/badge.svg)](https://github.com/noweh/twitter-api-v2-php/actions/workflows/run-tests.yml)
[![MIT Licensed](https://img.shields.io/github/license/noweh/twitter-api-v2-php)](licence.md)
[![last version](https://img.shields.io/packagist/v/noweh/twitter-api-v2-php)](https://packagist.org/packages/noweh/twitter-api-v2-php)
[![Downloads](https://img.shields.io/packagist/dt/noweh/twitter-api-v2-php)](https://packagist.org/packages/noweh/twitter-api-v2-php)
[![twitter](https://img.shields.io/twitter/follow/Noweh95?style=social)](https://twitter.com/Noweh95)

Twitter API V2 is a PHP package which provides an easy and fast access to Twitter REST API for Version 2 endpoints.

## Documentation

* [Installation](#installation)
* [Github Actions](#github-actions)
* [Usage](#usage)
    - [Active your developer account](#active-your-developer-account)
    - [Configuration setup](#configuration-setup)
    - [API Functionality](#api-functionality)
      - [Include the HTTP headers provided by Twitter in the response](#include-the-http-headers-provided-by-twitter-in-the-response)
* [Tweets endpoints](#tweet-endpoints)
    - [Timeline endpoints](#timeline-endpoints)
        - [Find Recent Mentioning for a User](#find-recent-mentioning-for-a-user)
        - [Find Recent Tweets for a User](#find-recent-tweets-for-a-user)
        - [Reverse Chronological Timeline by user ID](#reverse-chronological-timeline-by-user-id)
    - [Tweet/Likes endpoints](#tweetlikes-endpoints)
        - [Tweets liked by a user](#tweets-liked-by-a-user)
        - [Users who liked a tweet](#users-who-liked-a-tweet)
    - [Tweet/Lookup endpoints](#tweetlikes-endpoints)
        - [Search specific tweets](#search-specific-tweets)
        - [Find all replies from a Tweet](#find-all-replies-from-a-tweet)
    - [Tweet endpoints](#tweetlikes-endpoints)
        - [Fetch a tweet by Id](#fetch-a-tweet-by-id)
        - [Create a new Tweet](#create-a-new-tweet)
        - [Upload image to Twitter (and use in Tweets)](#upload-image-to-twitter-and-use-in-tweets)
    - [Tweet/Quotes endpoints](#tweetquotes-endpoints)
        - [Returns Quote Tweets for a Tweet specified by the requested Tweet ID](#returns-quote-tweets-for-a-tweet-specified-by-the-requested-tweet-id)
    - [Retweet endpoints](#retweet-endpoints)
        - [Retweet a Tweet](#retweet-a-tweet)
    - [Tweet/Replies endpoints](#tweetreplies-endpoints)
        - [Hide a reply to a Tweet](#hide-a-reply-to-a-tweet)
        - [Unhide a reply to a Tweet](#unhide-a-reply-to-a-tweet)
    - [Tweet/Bookmarks endpoints](#tweetbookmarks-endpoints)
        - [Lookup a user's Bookmarks](#lookup-a-users-bookmarks)
* [Users endpoints](#users-endpoints)
    - [User/Blocks endpoints](#userblocks-endpoints)
        - [Retrieve the users which you've blocked](#retrieve-the-users-which-youve-blocked)
    - [User/Follows endpoints](#userfollows-endpoints)
        - [Retrieve the users which are following you](#retrieve-the-users-which-are-following-you)
        - [Retrieve the users which you are following](#retrieve-the-users-which-you-are-following)
        - [Follow a user](#follow-a-user)
        - [Unfollow a user](#unfollow-a-user)
    - [User/Lookup endpoints](#userlookup-endpoints)
        - [Find me](#find-me) 
        - [Find Twitter Users](#find-twitter-users)
    - [User/Mutes endpoints](#usermutes-endpoints)
        - [Retrieve the users which you've muted](#retrieve-the-users-which-youve-muted)
        - [Mute user by username or ID](#mute-user-by-username-or-id)
        - [Unmute user by username or ID](#unmute-user-by-username-or-id)
* [Contributing](#contributing)
    - [To run test](#to-run-tests)
    - [To run code analyzer](#to-run-code-analyzer)

## Installation
To begin, you'll need to add the component to your `composer.json`
```shell
composer require noweh/twitter-api-v2-php
```
After adding the component, update your packages using `composer update` or install them using `composer install`.

## Github Actions

This repository uses [Github Actions](https://github.com/noweh/twitter-api-v2-php/actions) for each push/pull request, employing [PHPStan/PHPUnit](/.github/workflows/run-tests.yml).

Consequently, with each valid push, a new Tweet is posted from the [Twitter test account](https://twitter.com/canWeDeploy/status/1538477133487644672).

## Usage

### Active your developer account
Before anything else, you must follow [this tutorial](https://developer.twitter.com/en/docs/tutorials/getting-started-with-r-and-v2-of-the-twitter-api).
- [Request approval for a developer account](https://developer.twitter.com/en/apply-for-access);
- Once your developer account is approved, you will need to [create a Project](https://developer.twitter.com/en/docs/projects/overview);
- Enable read/write access for your Twitter app;
- Generate Consumer Keys and Authentication Tokens;
- Retrieve your Keys and Tokens from the Twitter Developer portal.

### Configuration setup
Expected settings are as follows:
```php
use Noweh\TwitterApi\Client;

$settings['account_id']
$settings['access_token'],
$settings['access_token_secret'],
$settings['consumer_key'],
$settings['consumer_secret'],
$settings['bearer_token'],
$settings['free_mode'] = false; // Optional

$client = new Client($settings);
```

### API Functionality
All API calls are triggered when the `performRequest()` method is invoked.
Depending on the context, this method can either be empty or contain data that will be sent as PostData (refer to examples of each call below).

#### Include the HTTP headers provided by Twitter in the response
The `performRequest()` method accepts a second parameter, `$withHeaders`, which is a boolean value.
Setting this parameter to `true` will include the headers information in the response.

Here are some examples of information included in headers:
- `x-rate-limit-limit`: the rate limit ceiling for that given endpoint
- `x-rate-limit-remaining`: the number of requests left for the 15-minute window
- `x-rate-limit-reset`: the remaining window before the rate limit resets, in UTC epoch seconds

Example:
```php
$response = $this->client->tweet()->create()
    ->performRequest([
        'text' => 'Test Tweet... '
    ],
    withHeaders: true)
;

/*
Output:
object(stdClass)#399 (2) {
    ["data"]=>
    object(stdClass)#398 (3) {
        ["edit_history_tweet_ids"]=>
        array(1) {
            [0]=>
            string(19) "1690304934837637121"
        }
        ["id"]=>
        string(19) "1690304934837637121"
        ["text"]=>
        string(39) "Test Tweet..."
    }
    ["headers"]=>
        ...
        ["x-rate-limit-limit"]=>
        array(1) {
            [0]=>
            string(5) "40000"
        }
        ["x-rate-limit-reset"]=>
        array(1) {
            [0]=>
            string(10) "1691835953"
        }
        ["x-rate-limit-remaining"]=>
        array(1) {
            [0]=>
            string(5) "39998"
        }
        ...
    }
}
*/
```
### Free mode

This API can be used in free mode, which allows for a limited usage of the API.
In this mode, the [Find me](#find-me) method is the only one that can be used.
You have to set the `free_mode` parameter to `true` when creating the client.

Example:
```php
...
$settings['free_mode'] = true;
$client = new Client($settings);
```

---
## Tweets endpoints

## Timeline endpoints

### Find Recent Mentioning for a User
Example:
```php
$return = $client->timeline()->getRecentMentions($accountId)->performRequest();
```

### Find Recent Tweets for a User
Example:
```php
$return = $client->timeline()->getRecentTweets($accountId)->performRequest();
```

### Reverse Chronological Timeline by user ID
Example:
```php
$return = $client->timeline()->getReverseChronological()->performRequest();
```

## Tweet/Likes endpoints

### Tweets liked by a user
Example:
```php
$return = $client->tweetLikes()->addMaxResults($pageSize)->getLikedTweets($accountId)->performRequest();
```

### Users who liked a tweet
Example:
```php
$return = $client->tweetLikes()->addMaxResults($pageSize)->getUsersWhoLiked($tweetId)->performRequest();
```

## Tweet/Lookup endpoints

### Search specific tweets
Example:
```php
$return = $client->tweetLookup()
    ->showMetrics()
    ->onlyWithMedias()
    ->addFilterOnUsernamesFrom([
        'twitterdev',
        'Noweh95'
    ], \Noweh\TwitterApi\TweetLookup::OPERATORS['OR'])
    ->addFilterOnKeywordOrPhrase([
        'Dune',
        'DenisVilleneuve'
    ], \Noweh\TwitterApi\TweetLookup::OPERATORS['AND'])
    ->addFilterOnLocales(['fr', 'en'])
    ->showUserDetails()
    ->performRequest()
;

$client->tweetLookup()
    ->addMaxResults($pageSize)
    ->addFilterOnKeywordOrPhrase($keywordFilter)
    ->addFilterOnLocales($localeFilter)
    ->showUserDetails()
    ->showMetrics()
    ->performRequest()
;
```

### Find all replies from a Tweet
```php
->addFilterOnConversationId($tweetId);
```

## Tweet endpoints

### Fetch a tweet by Id
Example:
```php
$return = $client->tweet()->->fetch(1622477565565739010)->performRequest();
```

### Create a new Tweet
Example:
```php
$return = $client->tweet()->create()->performRequest(['text' => 'Test Tweet... ']);
```

### Upload image to Twitter (and use in Tweets)
Example:
```php
$file_data = base64_encode(file_get_contents($file));
$media_info = $client->uploadMedia()->upload($file_data);
$return = $client->tweet()->create()
    ->performRequest([
        'text' => 'Test Tweet... ', 
        "media" => [
            "media_ids" => [
                (string)$media_info["media_id"]
            ]
        ]
    ])
;
```

## Tweet/Quotes endpoints

### Returns Quote Tweets for a Tweet specified by the requested Tweet ID
Example:
```php
$return = $client->tweetQuotes()->getQuoteTweets($tweetId)->performRequest();
```

## Retweet endpoints

### Retweet a Tweet
Example:
```php
$return = $client->retweet()->performRequest(['tweet_id' => $tweet_id]);
```

## Tweet/Replies endpoints

### Hide a reply to a Tweet
Example:
```php
$return = $client->->tweetReplies()->hideReply($tweetId)->performRequest(['hidden' => true]);
```

### Unhide a reply to a Tweet
Example:
```php
$return = $client->->tweetReplies()->hideReply($tweetId)->performRequest(['hidden' => false]);
```

## Tweet/Bookmarks endpoints

### Lookup a user's Bookmarks
Example:
```php
$return = $client->tweetBookmarks()->lookup()->performRequest();
```

---

## Users endpoints

## User/Blocks endpoints

### Retrieve the users which you've blocked
Example:
```php
$return = $client->userBlocks()->lookup()->performRequest();
```

## User/Follows endpoints

### Retrieve the users which are following you
Example:
```php
$return = $client->userFollows()->getFollowers()->performRequest();
```

### Retrieve the users which you are following
Example:
```php
$return = $client->userFollows()->getFollowing()->performRequest();
```

### Follow a user
Example:
```php
$return = $client->userFollows()->follow()->performRequest(['target_user_id' => $userId]);
```

### Unfollow a user
Example:
```php
$return = $client->userFollows()->unfollow($userId)->performRequest(['target_user_id' => self::$userId]);
```

## User/Lookup endpoints

### Find Me
Example:
```php
$return = $client->userMeLookup()->performRequest();
```

### Find Twitter Users
`findByIdOrUsername()` expects either an array, or a string.

You can specify the search mode as a second parameter (`Client::MODES['USERNAME']` OR `Client::MODES['ID']`)

Example:
```php
$return = $client->userLookup()
    ->findByIdOrUsername('twitterdev', \Noweh\TwitterApi\UserLookup::MODES['USERNAME'])
    ->performRequest()
;
```

## User/Mutes endpoints

### Retrieve the users which you've muted
Example:
```php
$return = $client->userMutes()->lookup()->performRequest();
```

### Mute user by username or ID
Example:
```php
$return = $client->userMutes()->mute()->performRequest(['target_user_id' => $userId]);
```

### Unmute user by username or ID
Example:
```php
$return = $client->userMutes()->unmute()->performRequest(['target_user_id' => $userId]);
```

---

## Contributing
Fork/download the code and run
```shell
composer install
```

copy `test/config/.env.example` to `test/config/.env` and add your credentials for testing.

### To run tests
```shell
./vendor/bin/phpunit
```

### To run code analyzer
```shell
./vendor/bin/phpstan analyse .
```