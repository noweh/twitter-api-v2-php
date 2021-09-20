<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Noweh\TwitterApi\TwitterSearch;

$apiBearerToken = 'XXX';

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

print_r($twitterReturns);
