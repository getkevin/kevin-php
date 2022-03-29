<?php

namespace Kevin;

/**
 * Class Account.
 */
class Account implements AccountInterface, EndpointInterface
{
    use AccountTrait;

    /**
     * Account constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param array  $options
     *
     * @throws KevinException
     */
    public function __construct($clientId = '', $clientSecret = '', $options = [])
    {
        $this->setOptionsAttributes($options);
        $this->setClientCredentials($clientId, $clientSecret);
        $this->initialize();
    }

    /**
     * API Method: Get account list.
     *
     * @see https://docs.kevin.eu/public/platform/v0.3#operation/getAccounts
     *
     * @param array $attr
     *
     * @return array
     *
     * @throws KevinException
     */
    public function getAccountList($attr = [])
    {
        $url = $this->getEndpointUrl(self::PATH_ACCOUNT_LIST);

        $data = '';

        $header = array_merge($this->getAccountHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get account details.
     *
     * @see https://docs.kevin.eu/public/platform/v0.3#operation/getAccount
     *
     * @param string $accountId
     * @param array  $attr
     *
     * @return array
     *
     * @throws KevinException
     */
    public function getAccountDetails($accountId, $attr = [])
    {
        $accountId = $this->escParam($accountId);

        $url = $this->getEndpointUrl(self::PATH_ACCOUNT_DETAILS);
        $url = $this->gluePath($url, $accountId);

        $data = '';

        $header = array_merge($this->getAccountHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get account transactions.
     *
     * @see https://docs.kevin.eu/public/platform/v0.3#operation/getAccountTransactions
     *
     * @param string $accountId
     * @param array  $attr
     *
     * @return array
     *
     * @throws KevinException
     */
    public function getAccountTransactions($accountId, $attr = [])
    {
        $accountId = $this->escParam($accountId);

        $url = $this->getEndpointUrl(self::PATH_ACCOUNT_TRANSACTIONS);
        $url = $this->gluePath($url, $accountId);

        $queryData = $this->getAccountTransactionsQueryAttr($attr);
        if (count($queryData)) {
            $query = http_build_query($queryData, '', '&');
            $url = $url.'?'.$query;
        }

        $data = '';

        $header = array_merge($this->getAccountHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get account balance.
     *
     * @see https://docs.kevin.eu/public/platform/v0.3#operation/getAccountBalance
     *
     * @param string $accountId
     * @param array  $attr
     *
     * @return array
     *
     * @throws KevinException
     */
    public function getAccountBalance($accountId, $attr = [])
    {
        $accountId = $this->escParam($accountId);

        $url = $this->getEndpointUrl(self::PATH_ACCOUNT_BALANCE);
        $url = $this->gluePath($url, $accountId);

        $data = '';

        $header = array_merge($this->getAccountHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }
}
