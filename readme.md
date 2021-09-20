# Twitter API V2 for PHP

[![Badge Twitter](https://img.shields.io/endpoint?url=https%3A%2F%2Ftwbadges.glitch.me%2Fbadges%2Fv2)](https://developer.twitter.com/en/docs/twitter-api)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](licence.md)

Twitter API V2 is a PHP package that provides an easy and fast access to Twitter REST API for Version 2 endpoints.

## Installation
First you need to add the component to your composer.json
```
composer require noweh/twitter-api-v2-php
```
Update your packages with *composer update* or install with *composer install*.

## How to use

### Active your developer account
In first, you need to follow [this tutorial](https://developer.twitter.com/en/docs/tutorials/getting-started-with-r-and-v2-of-the-twitter-api).
- [Request an approved account](https://developer.twitter.com/en/apply-for-access);
- Once you have an approved developer account, you will need to first [create a Project](https://developer.twitter.com/en/docs/projects/overview);
- Enable read/write access for your Twitter app;
- Grab your access Bearer Token from the twitter developer site.

### To search in Twitter
    use Noweh\TwitterApi\TwitterSearch;

Example:

    $apiBearerToken = '...'; // Previously retrieved from Twitter app

    $twitterReturns = (new TwitterSearch($apiBearerToken))
        ->showMetrics()
        ->onlyWithMedias()
        ->addFilterOnUsernamesFrom([
            'twitterdev',
            'Noweh95'
        ], TwitterSearch::OPERATORS['OR'])
        ->addFilterOnKeywordOrPhrase([
            'Dune',
            'DenisVilleneuve'
        ], TwitterSearch::OPERATORS['AND'])
        ->addFilterOnLocales(['fr', 'en'])
        ->showUserDetails()
        ->performRequest()
    ;
