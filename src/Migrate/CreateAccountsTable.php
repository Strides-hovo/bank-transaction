<?php

namespace App\Migrate;

use App\Migrate;

class CreateAccountsTable extends Migrate
{
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS accounts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    account VARCHAR(255) NOT NULL,
                    currency ENUM('EUR','CHF','USD','AMD') NOT NULL,
                    start_balance DECIMAL(12,2) NOT NULL DEFAULT 0,
                    UNIQUE KEY uniq_account_currency (account, currency)
                );";
        $this->db->exec($sql);
        return $this->info();
    }

    public function down()
    {
        $this->db->exec('DROP TABLE IF EXISTS accounts');
    }
}