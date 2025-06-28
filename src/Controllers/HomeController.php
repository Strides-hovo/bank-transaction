<?php

namespace App\Controllers;

use App\Repository\TransactionRepository;
use App\Services\TransactionService;
use Exception;



class HomeController
{


    /**
     * @var TransactionService
     */
    private $service;

    private $repository;
    /**
     * @var array
     */
    private $params;

    public function __construct()
    {
        $this->service = new TransactionService();
        $this->repository = new TransactionRepository();
        $this->params = $this->getParams();
    }

    /**
     * @throws Exception
     */
    public function index()
    {
        return render('home', [
            'title' => 'Home Page',
            'header' => 'Welcome to the Home Page ' ,

            'footer' => 'Footer content goes here',
        ]);
    }


    /** @method #POST url /import
     * @throws Exception
     */
    public function importFile()
    {
        $file = $this->params['excel'] ? : [];
        $filePath =  $this->service->importXLSXFile($file);
        $data = $this->service->parseFileData($filePath);
        $status = $this->repository->saveTransactions($data);

        return json_encode($status, JSON_PRETTY_PRINT);
    }


    /**
     * @return false|string
     * @method #GET url /transactions
     */
    public function getTransactions()
    {
        $transactions = $this->service->getAllTransactions();
        return json_encode($transactions, JSON_PRETTY_PRINT);
    }


    public function getAccountsData()
    {


        $result =  [
            ['bank' => 'Revolut', 'currency' => 'EUR', 'starting_balance' => 0, 'end_balance_chf' => 0],
            ['bank' => 'Revolut2', 'currency' => 'USD', 'starting_balance' => 0, 'end_balance_chf' => 0],
            ['bank' => 'Revolut3', 'currency' => 'EUR', 'starting_balance' => 0, 'end_balance_chf' => 0],
        ];


        return json_encode($result, JSON_PRETTY_PRINT);
    }


    private function getParams()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST') {
            return !empty($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
        }

        elseif ($method === 'GET') {
            return $_GET;
        }

        // for PUT, PATCH, DELETE
        $input = file_get_contents('php://input');

        parse_str($input, $parsed);
        return $parsed;
    }

}