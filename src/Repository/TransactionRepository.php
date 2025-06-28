<?php

namespace App\Repository;


use App\App;
use App\DB;
use Exception;
use PDO;

class TransactionRepository
{

    private $chunk = 50;


    /**
     * @var DB
     */
    private $db;


    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->db = App::getContainer('db');
    }


    /**
     * @throws Exception
     * @example [account => Revolut, number => 98989, currency => EUR, amount => 5.7, date => 2022-04-03 00:00:00]
     */
    public function saveTransactions(array $rows)
    {

        if (empty($rows)) {
            throw new Exception('No data to save');
        }

        /** @var $this DB */

        $headers = array_shift($rows);
        $chunk = array_chunk($rows, $this->chunk);
        $rowCount = 0;

        try {
            $this->db->beginTransaction();
            $this->db->exec('TRUNCATE TABLE `transactions`');
            foreach ($chunk as $item) {
                list($sql, $values) = $this->prepareTransactionSql($item, $headers);

                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
                $rowCount += $stmt->rowCount();
            }
            $this->db->commit();
        }
        catch (\PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }

        return [
            'status' => 'success',
            'message' => 'File imported successfully',
            'rowCount' => $rowCount
        ];
    }


    public function getAll()
    {
        return $this->db
            ->query('SELECT DISTINCT currency FROM transactions where currency != "CHF"')
            ->fetchAll(PDO::FETCH_COLUMN);
    }




    public function getLstAccounts()
    {

    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * @throws Exception
     */
    private function prepareTransactionSql(array $rows, array $headers)
    {
        $columns = $this->setColumns($headers);
        $values = [];
        $placeholders = [];
        $dateIndex = array_search('`date`', $columns, true);

        foreach ($rows as $row) {
            if ($dateIndex !== false && !validateDate($row[$dateIndex])) {
                continue;
            }

            $placeholders[] = '(' . implode(',', array_fill(0, count($row), '?')) . ')';
            foreach ($row as $value) {
                $values[] = $value;
            }
        }

        $sql = "INSERT INTO `transactions` (" . implode(',', $columns) . ") VALUES " . implode(',', $placeholders);
        return [$sql, $values];

    }


    /**
     * @throws Exception
     */
    private function setColumns(array $headers)
    {
        $columns = [];
        $map = [
            'Account' => '`account`',
            'Transaction No' => '`number`',
            'Amount' => '`amount`',
            'Currency' => '`currency`',
            'Date' => '`date`',
        ];

        if (count($headers) !== count($map)) {
            throw new Exception('Not valid headers');
        }
        foreach ($headers as $idx => $h) {
            if (isset($map[$h])) {
                $columns[$idx] = $map[$h];
            }
        }
        return $columns;
    }
}