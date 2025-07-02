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

    public function getChart()
    {
        $response = $this->repository->selectChartData();
        return $this->groupedChartData($response);
    }


    private function groupedChartData(array $data)
    {
        $categories     = $this->collectCategories();
        $monthlySums    = $this->collectSumByMonth($data);
        $balances       = $this->calculateBalance($monthlySums);
        $total          = $this->collectTotal($monthlySums);
        $series         = $this->collectSeries($balances, $total);

        return [
            'categories' => $categories,
            'series' => $series
        ];
    }


    /**
     * @param array $balances
     * @param $total
     * @return array @example [0][name => Stripe,data => [65,989]
     */
    private function collectSeries(array $balances, $total)
    {
        $series = [];
        foreach ($balances as $account => $months) {
            $dataPoints = [];
            for ($i = 1; $i <= 12; $i++) {
                $dataPoints[] = isset($months[$i]) ? $months[$i] : 0;
            }

            $series[] = array(
                'name' => $account,
                'data' => $dataPoints
            );
        }
        $totalData = [];
        for ($i = 1; $i <= 12; $i++) {
            $totalData[] = isset($total[$i]) ? $total[$i] : 0;
        }

        $series[] = array(
            'name' => 'Total',
            'data' => $totalData
        );
        return $series;
    }

    private function collectCategories()
    {
        $categories = [];
        for ($i = 1; $i <= 12; $i++) {
            $timestamp = strtotime("2024-$i-01");
            $categories[] = date('M', $timestamp);
        }
        return $categories;
    }


    /**
     * @param array $data
     * @return array
     * @example [Stripe][2] = 76
     */
    private function collectSumByMonth(array $data)
    {
        $monthlySums = [];
        foreach ($data as $item) {
            $account = $item['account'];
            $month = (int)date('n', strtotime($item['date']));

            if (!isset($monthlySums[$account][$month])) {
                $monthlySums[$account][$month] = 0;
            }

            $monthlySums[$account][$month] += $item['amount'];
        }
        return $monthlySums;
    }


    /**
     * @param array $monthlySums
     * @return array
     * @example [Stripe][2] = 78
     */
    private function calculateBalance(array $monthlySums)
    {
        $balances = [];
        foreach ($monthlySums as $account => $months) {
            $balance = 0;
            for ($i = 1; $i <= 12; $i++) {
                $amount = isset($months[$i]) ? $months[$i] : 0;
                $balance += $amount;
                $balances[$account][$i] = $balance;
            }
        }
        return $balances;
    }


    /**
     * @param array $monthlySums
     * @return array
     * @example [2] = 780
     */
    private function collectTotal(array $monthlySums)
    {
        $total = [];
        foreach ($monthlySums as $months) {
            $balance = 0;
            for ($i = 1; $i <= 12; $i++) {
                $amount = isset($months[$i]) ? $months[$i] : 0;
                $balance += $amount;
                if (!isset($total[$i])) {
                    $total[$i] = 0;
                }
                $total[$i] += $balance;
            }
        }
        return $total;
    }
}