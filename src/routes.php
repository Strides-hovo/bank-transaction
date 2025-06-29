<?php
return array(
    '/'       => array('GET'  => 'HomeController@index'),
    '/import' => array('POST' => 'HomeController@importFile'),
    '/transactions' => array('GET' => 'HomeController@getTransactions'),
    '/accounts' => array('GET' => 'HomeController@getAccountsData'),
    '/update-balance' => array('POST' => 'HomeController@updateStartBalance'),
);
