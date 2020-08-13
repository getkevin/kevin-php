<?php

namespace Kevin;

class Client
{
    use UtilityTrait;

    /**
     * Auth instance.
     *
     * @var Auth
     */
    private $auth;

    /**
     * Payment instance.
     *
     * @var Payment
     */
    private $payment;

    /**
     * Client constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param array $options
     * @throws KevinException
     */
    public function __construct($clientId = '', $clientSecret = '', $options = [])
    {
        $this->setOptionsAttributes($options);
        $this->setClientCredentials($clientId, $clientSecret);
        $this->initialize();
    }

    /**
     * Returns Auth instance.
     *
     * @return Auth
     * @throws KevinException
     */
    public function auth()
    {
        $this->auth = isset($this->auth) ? $this->auth : new Auth($this->clientId, $this->clientSecret, $this->options);

        return $this->auth;
    }

    /**
     * Returns Payment instance.
     *
     * @return Payment
     * @throws KevinException
     */
    public function payment()
    {
        $this->payment = isset($this->payment) ? $this->payment : new Payment($this->clientId, $this->clientSecret, $this->options);

        return $this->payment;
    }
}
