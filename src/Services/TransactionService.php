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
     * @throws Exception
     */
    public function importXLSXFile($file)
    {
        $fileName = $file['name'];
        $uploadDir = __DIR__ . '/../../public/uploads/';
        $uploadFile = $uploadDir . basename($fileName);
        $fileTmpName = $file['tmp_name'];

        if (move_uploaded_file($fileTmpName, $uploadFile)) {
            return $uploadFile;
        }

        throw new Exception('File upload failed. Please try again.');
    }


    /**
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Exception
     * @throws Exception
     */
    public function parseFileData($filePath)
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


    public function getAllTransactions()
    {
        $currencies = $this->repository->getAll();
        $result = array_map(function ($currency){
            return [
                'Currency' => $currency,
                'Fx Rate' => $this->fetchRateToCHF($currency)
            ];

        }, $currencies);

        return $result;
    }


    private function fetchRateToCHF( $currency)
    {
        $apiKey = '9c939dba92545858a6cf81d5';

        $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/pair/{$currency}/CHF";
        $resp = json_decode(file_get_contents($url), true);

        return $resp['conversion_rate'];
    }
}