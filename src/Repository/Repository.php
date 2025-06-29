<?php

namespace App\Repository;

use App\App;
use App\DB;
use Exception;

abstract class Repository
{
    /**
     * @var DB
     */
    protected $db;


    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->db = App::getContainer('db');
    }

    /**
     * @throws Exception
     */
    protected function setTransactionColumns(array $headers)
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