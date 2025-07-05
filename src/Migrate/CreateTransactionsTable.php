<?php

namespace App\Migrate;

use App\Migrate;

class CreateTransactionsTable extends Migrate {


    public function up()
    {

        $sql = "CREATE TABLE IF NOT EXISTS transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            account VARCHAR(255) NOT NULL,
            number VARCHAR(100) NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            currency ENUM('EUR','CHF','USD','AMD') NOT NULL,
            date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $this->db->exec($sql);
        return $this->info();
    }


    public function down()
    {
        $this->db->exec('DROP TABLE IF EXISTS transactions');
    }
}