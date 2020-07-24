<?php

namespace Kevin;

/**
 * Interface to provide base list of authentication related methods.
 */
interface AuthInterface
{
    public function getCountries();

    public function getBanks($attr = []);

    public function getBank($bankId);

    public function auth($attr = []);

    public function receiveToken($attr = []);

    public function refreshToken($attr = []);

    public function receiveTokenContent($attr = []);
}
