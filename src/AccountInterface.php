<?php

namespace Kevin;

/**
 * Interface to provide base list of account related methods.
 */
interface AccountInterface
{
    public function getAccountList($attr = []);

    public function getAccountDetails($accountId, $attr = []);

    public function getAccountTransactions($accountId, $attr = []);

    public function getAccountBalance($accountId, $attr = []);
}
