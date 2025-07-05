<?php

namespace App;

use Exception;

abstract class Migrate
{
    /**
     * @var DB|null
     */
    protected $db;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->db = app(DB::class)->getContainer()[DB::class];
    }

    protected function info()
    {
        echo "migrate successfully" . PHP_EOL . $this->db->lastInsertId();
    }

    abstract public function up();
    abstract public function down();
}