<?php

namespace App;

use PDO;

class DB extends PDO
{
    public function __construct($dsn, $username = null, $password = null)
    {
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];
        parent::__construct($dsn, $username, $password, $options);
    }



    public function createTableTransactions()
    {
        $sql = "CREATE TABLE IF NOT EXISTS transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            account VARCHAR(255) NOT NULL,
            number VARCHAR(100) NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            currency ENUM('EUR','CHF','USD') NOT NULL,
            date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $this->exec($sql);
        return $this;
    }


    public function createTableAccounts()
    {
        $sql = "CREATE TABLE IF NOT EXISTS accounts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    account VARCHAR(255) NOT NULL,
                    currency ENUM('EUR','CHF','USD') NOT NULL,
                    start_balance DECIMAL(12,2) NOT NULL DEFAULT 0,
                    UNIQUE KEY uniq_account_currency (account, currency)
                );";
        $this->exec($sql);
        return $this;
    }
}
