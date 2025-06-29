<?php

namespace App\Controllers;

use App\Services\AccountService;
use App\Services\ImportService;
use App\Services\TransactionService;
use Exception;


class HomeController
{

    private $params;
    private $transactionService;
    private $accountService;


    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->accountService = new AccountService();
        $this->params = $this->getParams();
    }

    /**
     * @throws Exception
     */
    public function index()
    {
        return render('home', [
            'title' => 'Home Page',
            'header' => 'Welcome to the Home Page ',
            'footer' => 'Footer content',
        ]);
    }


    /** @method #POST url /import
     * @throws Exception
     */
    public function importFile()
    {
        $file = $this->params['excel'] ?: [];
        $filePath = ImportService::importXLSXFile($file);
        $status = $this->transactionService->saveTransactions($filePath);

        return json_encode($status, JSON_PRETTY_PRINT);
    }


    /**
     * @return false|string
     * @method #GET url /transactions
     */
    public function getRates()
    {
        $transactions = $this->transactionService->getRates();
        return json_encode($transactions, JSON_PRETTY_PRINT);
    }


    public function getAccountsData()
    {
        return json_encode($this->accountService->getAccountsList(), JSON_PRETTY_PRINT);
    }


    public function updateStartBalance()
    {
        $response = $this->accountService->update($this->params);

        return json_encode($response, JSON_PRETTY_PRINT);
    }


    public function getTransactions()
    {
        return json_encode($this->transactionService->getTransactions(), JSON_PRETTY_PRINT);
    }


    /**
     * @throws Exception
     */
    public function updateTransaction()
    {
        return json_encode($this->transactionService->updateTransaction($this->params), JSON_PRETTY_PRINT);
    }


    private function getParams()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $contentType = $_SERVER['CONTENT_TYPE'] ?: '';
        $input = file_get_contents('php://input'); // for PUT, PATCH, DELETE

        if (stripos($contentType, 'application/json') !== false) {
            return json_decode($input, true) ?: [];
        }
        if ($method === 'POST') {
            return !empty($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
        } elseif ($method === 'GET') {
            return $_GET;
        }

        parse_str($input, $parsed);
        return $parsed;
    }
}