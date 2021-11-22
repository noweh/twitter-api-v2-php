# Twitter API V2 for PHP

![php](https://img.shields.io/badge/PHP-v7.3-828cb7.svg?style=flat-square)
[![Badge Twitter](https://img.shields.io/endpoint?url=https%3A%2F%2Ftwbadges.glitch.me%2Fbadges%2Fv2)](https://developer.twitter.com/en/docs/twitter-api)
[![Run Tests](https://github.com/noweh/twitter-api-v2-php/actions/workflows/run-tests.yml/badge.svg)](https://github.com/noweh/twitter-api-v2-php/actions/workflows/run-tests.yml)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](licence.md)

Twitter API V2 is a PHP package which provides an easy and fast access to Twitter REST API for Version 2 endpoints.


## Installation
First, you need to add the component to your composer.json
```
composer require noweh/twitter-api-v2-php
```
Update your packages with *composer update* or install with *composer install*.

## How to use

### Active your developer account
Firstly, you need to follow [this tutorial](https://developer.twitter.com/en/docs/tutorials/getting-started-with-r-and-v2-of-the-twitter-api).
- [Request of an approved account](https://developer.twitter.com/en/apply-for-access);
- Once you have an approved developer account, you will need to [create a Project](https://developer.twitter.com/en/docs/projects/overview);
- Enable read/write access for your Twitter app;
- Generate Consumer Keys and Authentication Tokens;
- Grab your Keys and Tokens from the twitter developer site.

### Prepare settings
Settings are expected as below:

    $settings['access_token'],
    $settings['access_token_secret'],
    $settings['consumer_key'],
    $settings['consumer_secret'],
    $settings['bearer_token']

### To search specific tweets
    use Noweh\TwitterApi\TweetSearch;

Example:

    $settings = ['...', '...']; // Previously retrieved from Twitter app

    $return = (new TweetSearch($settings))
        ->showMetrics()
        ->onlyWithMedias()
        ->addFilterOnUsernamesFrom([
            'twitterdev',
            'Noweh95'
        ], TweetSearch::OPERATORS['OR'])
        ->addFilterOnKeywordOrPhrase([
            'Dune',
            'DenisVilleneuve'
        ], TweetSearch::OPERATORS['AND'])
        ->addFilterOnLocales(['fr', 'en'])
        ->showUserDetails()
        ->performRequest()
    ;

### To find Twitter Users
    use Noweh\TwitterApi\UserSearch;

`findByIdOrUsername()` expects either an array, or a string.

You can specify the search mode as a second parameter (`UserSearch::MODES['USERNAME']` OR `UserSearch::MODES['ID']`)

Example:

    $settings = ['...', '...']; // Previously retrieved from Twitter app

    $return = (new UserSearch($settings))
        ->findByIdOrUsername('twitterdev', UserSearch::MODES['USERNAME'])
        ->performRequest()
    ;

### To Post a new Tweet
    use Noweh\TwitterApi\Tweet;

You have to add your account ID in settings for Oauth1.0a

    $settings['account_id']

Example:

    $settings = ['...', '...']; // Previously retrieved from Twitter app

    $tweet = new Tweet($this->settings);
    $return = $tweet->performRequest('POST', ['text' => 'This is a test....']);

### To Retweet
    use Noweh\TwitterApi\Retweet;

You have to add your account ID in settings for Oauth1.0a
    
    $settings['account_id']

Example:
    
    $settings = ['...', '...']; // Previously retrieved from Twitter app

    $retweeter = new Retweet($this->settings);
    $return = $retweeter->performRequest('POST', ['tweet_id' => $tweet->id]);

## Contributing
Fork/download the code and run

`composer install`

copy `.env.example` to `.env` and add your credentials for testing.

To run tests

`./vendor/bin/phpunit`