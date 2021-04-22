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
        $url = $this->getEndpointUrl(self::PATH_INIT_PAYMENT);

        $queryData = $this->getPaymentQueryAttr($attr);
        if (count($queryData)) {
            $query = http_build_query($queryData, '', '&');
            $url = $url . '?' . $query;
        }

        $jsonData = $this->getInitPaymentBodyAttr($attr);
        $data = json_encode($jsonData, JSON_FORCE_OBJECT);

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

        $url = $this->getEndpointUrl(self::PATH_PAYMENT);
        $url = $this->gluePath($url, $paymentId);

        $data = '';

        $header = array_merge($this->getPaymentHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get payment status
     * @see https://docs.getkevin.eu/public/platform/v0.1#operation/getPaymentStatus
     *
     * @param string $paymentId
     * @param array $attr
     * @return array
     * @throws KevinException
     */
    public function getPaymentStatus($paymentId, $attr = [])
    {
        $paymentId = $this->escParam($paymentId);

        $url = $this->getEndpointUrl(self::PATH_PAYMENT_STATUS);
        $url = $this->gluePath($url, $paymentId);

        $data = '';

        $header = array_merge($this->getPaymentStatusHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Initiate payment refund
     * @see https://docs.getkevin.eu/public/platform/v0.3#operation/initiatePaymentRefund
     *
     * @param string $paymentId
     * @param array $attr
     * @return array
     * @throws KevinException
     */
    public function initiatePaymentRefund($paymentId, $attr = [])
    {
        $paymentId = $this->escParam($paymentId);

        $url = $this->getEndpointUrl(self::PATH_INITIATE_PAYMENT_REFUND);
        $url = $this->gluePath($url, $paymentId);

        $jsonData = $this->getInitPaymentRefundAttr($attr);
        $data = json_encode($jsonData);

        $header = array_merge($this->getInitiatePaymentRefundHeaderAttr($attr), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'POST', $data, $header);

        return $this->buildResponse($response);
    }

    /**
     * API Method: Get payment refunds
     * @see https://docs.getkevin.eu/public/platform/v0.3#operation/getPaymentRefunds
     *
     * @param string $paymentId
     * @param array $attr
     * @return array
     * @throws KevinException
     */
    public function getPaymentRefunds($paymentId)
    {
        $paymentId = $this->escParam($paymentId);

        $url = $this->getEndpointUrl(self::PATH_GET_PAYMENT_REFUNDS);
        $url = $this->gluePath($url, $paymentId);

        $data = '';

        $header = array_merge($this->buildHeader(), $this->buildJsonHeader($data));

        $response = $this->buildRequest($url, 'GET', $data, $header);

        return $this->buildResponse($response);
    }
}
