<?php

namespace App\Repository;


use App\DB;
use Exception;
use PDO;

class TransactionRepository extends Repository
{

    private $chunk = 50;


    /**
     * @throws Exception
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
            $accounts = (new AccountRepository())->saveAccounts($rows, $headers);

            foreach ($chunk as $item) {
                list($sql, $values) = $this->prepareTransactionSql($item, $headers);

                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
                $rowCount += $stmt->rowCount();
            }
            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }

        return [
            'status' => 'success',
            'message' => 'File imported successfully',
            'rowCount' => $rowCount,
            'accounts' => $accounts
        ];
    }


    public function getCurrencies()
    {
        return $this->db
            ->query('SELECT DISTINCT currency FROM transactions where currency != "CHF"')
            ->fetchAll(PDO::FETCH_COLUMN);
    }


    public function getAll()
    {
        return $this->db
            ->query('SELECT * FROM transactions')
            ->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateTransaction(array $data)
    {

        $id = $data['id'];
        unset($data['id']);
        $values = array_values($data);

        $prepare = "UPDATE transactions SET ";
        foreach ($data as $k => $v) {
            $prepare .= "`{$k}` = ?, ";
        }
        $prepare = rtrim($prepare, ', ') . " WHERE id = ?";
        $values[] = $id;

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare($prepare);
            $stmt->execute($values);
            $this->db->commit();
            return $stmt->rowCount();
        }
        catch (\PDOException $e){
            $this->db->rollBack();
            return 0;
        }
    }

    /**
     * @throws Exception
     */
    private function prepareTransactionSql(array $rows, array $headers)
    {
        $columns = $this->setTransactionColumns($headers);
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
}