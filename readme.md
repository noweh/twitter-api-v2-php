# Twitter API V2 for PHP

![php](https://img.shields.io/badge/PHP-v7.4-828cb7.svg?style=flat-square)
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

## Github Actions

This repository uses [Github Actions](https://github.com/noweh/twitter-api-v2-php/actions) for each push/pull request with [PHPStan/PHPUnit](/.github/workflows/run-tests.yml).

Therefore, for each valid push, a new Tweet is posted from my [Twitter test account](https://twitter.com/canWeDeploy/status/1538477133487644672).

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

    use Noweh\TwitterApi\Client;

    $settings['account_id']
    $settings['access_token'],
    $settings['access_token_secret'],
    $settings['consumer_key'],
    $settings['consumer_secret'],
    $settings['bearer_token']

    $client = new Client($settings);

### To fetch a tweet by Id
Example:

    $result = $client->tweet()->performRequest('GET', array( 'id' => $id));

### To search specific tweets
Example:

    $return = $client->tweetSearch()
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
`findByIdOrUsername()` expects either an array, or a string.

You can specify the search mode as a second parameter (`Client::MODES['USERNAME']` OR `Client::MODES['ID']`)

Example:

    $return = $client->userSearch()
        ->findByIdOrUsername('twitterdev', UserSearch::MODES['USERNAME'])
        ->performRequest()
    ;

### To find Recent Mentioning for a User
Example:

    $return = $client->timeline()->findRecentMentioningForUserId('1538300985570885636')->performRequest();

### To Post a new Tweet
Example:

    $return = $client->tweet()->performRequest('POST', ['text' => 'This is a test....']);

### To Retweet

Example:
    
    $return = $client->retweet()->performRequest('POST', ['tweet_id' => $tweet->id]);

## Contributing
Fork/download the code and run

`composer install`

copy `test/config/.env.example` to `test/config/.env` and add your credentials for testing.

### To run tests

`./vendor/bin/phpunit`

### To run code analyzer

`./vendor/bin/phpstan analyse .`
