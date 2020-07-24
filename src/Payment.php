<?php

namespace Kevin;

/**
 * Class Payment.
 *
 * @package Kevin
 */
class Payment implements PaymentInterface, EndpointInterface
{
    use PaymentTrait;

    /**
     * Payment constructor.
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
     * API Method: Initiate payment.
     * @see https://docs.getkevin.eu/public/platform/v0.1#operation/initiatePayment
     *
     * @param array $attr
     * @return array
     * @throws KevinException
     */
    public function initPayment($attr = [])
    {
        $url = self::URL_INIT_PAYMENT;

        $queryData = $this->getPaymentQueryAttr($attr);
        if (count($queryData)) {
            $query = http_build_query($queryData, '', '&');
            $url = $url . '?' . $query;
        }

        $jsonData = $this->getInitPaymentBodyAttr($attr);
        $data = json_encode($jsonData);

        $header = array_merge($this->getInitPaymentHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'POST', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get payment.
     * @see https://docs.getkevin.eu/public/platform/v0.1#operation/getPayment
     *
     * @param $paymentId
     * @param array $attr
     * @return array
     * @throws KevinException
     */
    public function getPayment($paymentId, $attr = [])
    {
        $paymentId = $this->escParam($paymentId);

        $url = $this->gluePath(self::URL_PAYMENT, $paymentId);

        $data = '';

        $header = array_merge($this->getPaymentHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get payment status
     * @see https://docs.getkevin.eu/public/platform/v0.1#operation/getPaymentStatus
     *
     * @param $paymentId
     * @param array $attr
     * @return array
     * @throws KevinException
     */
    public function getPaymentStatus($paymentId, $attr = [])
    {
        $paymentId = $this->escParam($paymentId);

        $url = $this->gluePath(self::URL_PAYMENT_STATUS, $paymentId);

        $data = '';

        $header = array_merge($this->getPaymentStatusHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }
}
