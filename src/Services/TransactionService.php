<?php

namespace App\Services;

use App\Repository\TransactionRepository;
use Exception;
use PHPExcel_Exception;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Exception;

class TransactionService
{


    /**
     * @var TransactionRepository
     */
    private $repository;

    public function __construct()
    {
        $this->repository = new TransactionRepository();
    }


    /**
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Exception
     * @throws Exception
     */
    public function saveTransactions($filePath)
    {
        $data = $this->parseFileDataToArray($filePath);
        return $this->repository->saveTransactions($data);
    }


    public function getRates()
    {
        $currencies = $this->repository->getCurrencies();
        return array_map(function ($currency) {
            return [
                'Currency' => $currency,
                'Fx Rate' => FetchService::fetchRateToCHF($currency)
            ];

        }, $currencies);
    }


    public function getTransactions()
    {
        return $this->repository->getAll();
    }


    /**
     * @throws Exception
     */
    public function updateTransaction(array $data)
    {
        $ids = array_keys($data['data']);
        $id = $ids[0];
        $fields = $data['data'][$id];
        $fields['id'] = $id;
        $response = [
            'status' => 'success',
            'message' => 'Transaction updated successfully',
        ];
        $errorResponse = [
            'status' => 'error',
            'message' => 'date is not valid',
        ];
        if (isset($fields['date']) && !validateDate($fields['date'], 'Y-m-d')) {
            http_response_code(419);
            return $errorResponse;
        }
        if ($this->repository->updateTransaction($fields)) {
            return $response;
        }
        http_response_code(419);
        return $errorResponse;
    }


    /**
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Exception
     * @throws Exception
     */
    private function parseFileDataToArray($filePath)
    {

        if (!file_exists($filePath)) {
            throw new Exception('File not found: ' . $filePath);
        }
        $excel = PHPExcel_IOFactory::load($filePath);
        $sheet = $excel->getActiveSheet();

        $rows = [];
        foreach ($sheet->getRowIterator() as $row) {
            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = $cell->getValue();
            }
            $rows[] = array_filter($cells);
        }

        return $rows;
    }


}