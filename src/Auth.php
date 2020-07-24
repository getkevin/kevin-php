<?php

namespace Kevin;

/**
 * Class Auth.
 *
 * @package Kevin
 */
class Auth implements AuthInterface, EndpointInterface
{
    use AuthTrait;

    /**
     * Auth constructor.
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
     * API Method: Get supported countries.
     * @see https://docs.getkevin.eu/public/platform/v0.1#operation/getCountries
     *
     * @return array
     * @throws KevinException
     */
    public function getCountries()
    {
        $url = self::URL_COUNTRIES;

        $data = '';

        $header = array_merge($this->buildHeader(), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get supported banks.
     * @see https://docs.getkevin.eu/public/platform/v0.1#operation/getBanks
     *
     * @param array $attr
     * @return array
     * @throws KevinException
     */
    public function getBanks($attr = [])
    {
        $url = self::URL_BANKS;

        $queryData = $this->getBankQueryAttr($attr);
        if (count($queryData)) {
            $query = http_build_query($queryData, '', '&');
            $url = $url . '?' . $query;
        }

        $data = '';

        $header = array_merge($this->buildHeader(), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get supported bank.
     * @see: https://docs.getkevin.eu/public/platform/v0.1#operation/getBank
     *
     * @param string $bankId
     * @return array
     * @throws KevinException
     */
    public function getBank($bankId)
    {
        $bankId = $this->escParam($bankId);

        $url = $this->gluePath(self::URL_BANK, $bankId);

        $data = '';

        $header = array_merge($this->buildHeader(), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Start authentication.
     * @see https://docs.getkevin.eu/public/platform/v0.1#operation/startAuth
     *
     * @param array $attr
     * @return array
     * @throws KevinException
     */
    public function auth($attr = [])
    {
        $url = self::URL_AUTH;

        $queryData = $this->getAuthQueryAttr($attr);
        if (count($queryData)) {
            $query = http_build_query($queryData, '', '&');
            $url = $url . '?' . $query;
        }

        $jsonData = $this->getAuthBodyAttr($attr);
        $data = json_encode($jsonData);

        $header = array_merge($this->getAuthHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'POST', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Start authentication.
     *
     * @param array $attr
     * @return array
     * @throws KevinException
     *
     * @see \Kevin\Auth::auth();
     */
    public function authenticate($attr = [])
    {
        return $this->auth($attr);
    }

    /**
     * API Method: Receive token.
     * @see https://docs.getkevin.eu/public/platform/v0.1#operation/receiveToken
     *
     * @param array $attr
     * @return array|string
     * @throws KevinException
     */
    public function receiveToken($attr = [])
    {
        $url = self::URL_RECEIVE_TOKEN;

        $jsonData = $this->getReceiveTokenBodyAttr($attr);
        $data = json_encode($jsonData);

        $header = array_merge($this->buildHeader(), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'POST', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Refresh token.
     * @see https://docs.getkevin.eu/public/platform/v0.1#operation/receiveToken
     *
     * @param array $attr
     * @return array|string
     * @throws KevinException
     */
    public function refreshToken($attr = [])
    {
        $url = self::URL_REFRESH_TOKEN;

        $jsonData = $this->getRefreshTokenBodyAttr($attr);
        $data = json_encode($jsonData);

        $header = array_merge($this->buildHeader(), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'POST', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Receive token content.
     * @see https://docs.getkevin.eu/public/platform/v0.1#operation/receiveTokenContent
     *
     * @param array $attr
     * @return array|string
     * @throws KevinException
     */
    public function receiveTokenContent($attr = [])
    {
        $url = self::URL_TOKEN_CONTENT;

        $data = '';

        $header = array_merge($this->getReceiveTokenContentHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }
}
