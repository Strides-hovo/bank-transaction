<?php
return array(
    '/'       => array('GET'  => 'HomeController@index'),
    '/import' => array('POST' => 'HomeController@importFile'),
    '/rates' => array('GET' => 'HomeController@getRates'),
    '/accounts' => array('GET' => 'HomeController@getAccountsData'),
    '/balance/update' => array('POST' => 'HomeController@updateStartBalance'),
    '/transactions' => array('GET' => 'HomeController@getTransactions'),
    '/transactions/update' => array('POST' => 'HomeController@updateTransaction'),
);
