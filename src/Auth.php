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
        $url = $this->getEndpointUrl(self::PATH_COUNTRIES);

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
        $url = $this->getEndpointUrl(self::PATH_BANKS);

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

        $url = $this->getEndpointUrl(self::PATH_BANK);
        $url = $this->gluePath($url, $bankId);

        $data = '';

        $header = array_merge($this->buildHeader(), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get supported bank by card number piece.
     * @see: https://docs.getkevin.eu/public/platform/v0.3#operation/getBankByCardNumberPiece
     *
     * @param string $cardNumberPiece
     * @return array
     * @throws KevinException
     */
    public function getBankByCardNumberPiece($cardNumberPiece)
    {
        $cardNumberPiece = $this->escParam($cardNumberPiece);

        $url = $this->getEndpointUrl(self::PATH_BANK_BY_CARD_NUMBER_PIECE);
        $url = $this->gluePath($url, $cardNumberPiece);

        $data = '';

        $header = array_merge($this->buildHeader(), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get supported payment methods.
     * @see https://docs.getkevin.eu/public/platform/v0.3#operation/getPaymentMethods
     *
     * @return array
     * @throws KevinException
     */
    public function getPaymentMethods()
    {
        $url = $this->getEndpointUrl(self::PATH_PAYMENT_METHODS);

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
        $url = $this->getEndpointUrl(self::PATH_AUTH);

        $queryData = $this->getAuthQueryAttr($attr);
        if (count($queryData)) {
            $query = http_build_query($queryData, '', '&');
            $url = $url . '?' . $query;
        }

        $jsonData = $this->getAuthBodyAttr($attr);

        $data = json_encode($jsonData, JSON_FORCE_OBJECT);

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
        $url = $this->getEndpointUrl(self::PATH_RECEIVE_TOKEN);

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
        $url = $this->getEndpointUrl(self::PATH_REFRESH_TOKEN);

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
        $url = $this->getEndpointUrl(self::PATH_TOKEN_CONTENT);

        $data = '';

        $header = array_merge($this->getReceiveTokenContentHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }
}
