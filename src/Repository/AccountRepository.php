<?php

namespace App\Repository;


use Exception;

class AccountRepository extends Repository
{

    /**
     * @return int
     *@throws Exception
     */
    public function saveAccounts(array $rows, array $headers)
    {
        list($values, $sql) = $this->prepareAccountSql($headers, $rows);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        return $stmt->rowCount();
    }


    public function selectLstAccounts()
    {
        $sql = "
            SELECT
                a.id,
                a.account bank,
                a.currency,
                a.start_balance,
                (a.start_balance + IFNULL(SUM(t.amount), 0)) AS end_balance
            FROM accounts a
            LEFT JOIN transactions t ON a.account = t.account and a.currency = t.currency
            GROUP BY a.id, a.account, a.currency, a.start_balance;
            ";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function updateStartBalanceWithAccount(array $data)
    {
        $this->db->beginTransaction();

        try {
            // get old name of account
            $stmt = $this->db->prepare("SELECT account FROM accounts WHERE id = ?");
            $stmt->execute([$data['id']]);
            $oldAccount = $stmt->fetchColumn();


            // Update name and balance
            $stmt = $this->db->prepare("UPDATE accounts SET account = ?, start_balance = ? WHERE id = ?");
            $stmt->execute([
                $data['account'],
                $data['start_balance'],
                $data['id']
            ]);
            $rowCount = $stmt->rowCount();
            // rename name account from transaction
            $stmt = $this->db->prepare("UPDATE transactions SET account = ? WHERE account = ?");
            $stmt->execute([
                $data['account'],
                $oldAccount
            ]);

            $this->db->commit();
            return $rowCount;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return 0;
        }


    }

    /**
     * @param array $headers
     * @param array $rows
     * @return array
     * @throws Exception
     */
    private function prepareAccountSql(array $headers, array $rows)
    {
        $columns = $this->setTransactionColumns($headers);
        $accountIndex = array_search('`account`', $columns, true);
        $currencyIndex = array_search('`currency`', $columns, true);
        if ($accountIndex === false || $currencyIndex === false) {
            throw new Exception('column account dont have');
        }

        $columns = array_map(function ($item) use ($accountIndex,$currencyIndex ) {
            return [
                $item[$accountIndex],
                $item[$currencyIndex]
            ];
        }, $rows);

        $uniqueColumns = array_map("unserialize", array_values(array_unique(array_map("serialize", $columns))));
        $placeholders = array_fill(0, count($uniqueColumns), '(?,?)');
        $values = array_merge(...$uniqueColumns);

        $sql = "INSERT INTO `accounts` (`account`, `currency`) VALUES " . implode(',', $placeholders) . "
        ON DUPLICATE KEY UPDATE start_balance = start_balance";

        return array($values, $sql);
    }
}