<?php

namespace App\Services;

use App\Repository\AccountRepository;

class AccountService
{

    private $repository;
    public function __construct()
    {
        $this->repository = new AccountRepository();
    }


    public function getAccountsList()
    {
         $data = $this->repository->selectLstAccounts();

         return array_map(function ($item){
             $item['end_balance_chf'] = FetchService::fetchRateToCHF($item['currency']);

             return $item;
         }, $data);
    }

    public function update(array $data)
    {
        if ($this->repository->updateStartBalanceWithAccount($data)){
            return [
                'status' => 'success',
                'message' => 'Account updated successfully',
            ];
        }
        http_response_code(419);
        return [
            'status' => 'error',
            'message' => 'Account failed update',
        ];
    }

}