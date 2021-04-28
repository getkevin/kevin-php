<?php

namespace Kevin;

/**
 * Trait providing helper methods used for payments.
 *
 * @package Kevin
 */
trait PaymentTrait
{
    use UtilityTrait;

    /**
     * Extract query attributes used for payment action.
     *
     * @param array $attr
     * @return array
     */
    private function getPaymentQueryAttr($attr = [])
    {
        $data = [];

        if (isset($attr['bankId'])) {
            $data['bankId'] = strval($attr['bankId']);
        }

        if (isset($attr['redirectPreferred'])) {
            $data['redirectPreferred'] = boolval(filter_var($attr['redirectPreferred'], FILTER_VALIDATE_BOOLEAN)) ? 'true' : 'false';
        }

        if (isset($attr['paymentMethodPreferred'])) {
            $data['paymentMethodPreferred'] = strval($attr['paymentMethodPreferred']);
        }

        return $data;
    }

    /**
     * Extract body attributes used for payment action.
     *
     * @param array $attr
     * @return array
     */
    private function getInitPaymentBodyAttr($attr)
    {
        $schema = [
            'creditorName' => '',
            'creditorAccount' => [
                'iban' => '',
                'bban' => '',
                'sortCodeAccountNumber' => '',
            ],
            'debtorAccount' => [
                'iban' => '',
                'bban' => '',
                'sortCodeAccountNumber' => '',
            ],
            'bankPaymentMethod' => [
                'creditorName' => '',
                'endToEndId' => '',
                'informationStructured' => [
                    'reference' => '',
                ],
                'creditorAccount' => [
                    'iban' => '',
                ]
            ],
            'cardPaymentMethod' => [
                'cvc' => '',
                'expMonth' => '',
                'expYear' => '',
                'number' => '',
                'holderName' => '',
            ],
            'amount' => '',
            'currencyCode' => '',
            'description' => '',
            'endToEndId' => '',
            'informationUnstructured' => '',
            'informationStructured' => [
                'reference' => '',
                'referenceType' => '',
            ],
            'requestedExecutionDate' => '',
            'identifier' => [
                'email' => '',
            ]
        ];

        return $this->processSchemaAttributes($schema, $attr);
    }

    /**
     * Extract body attributes used for init payment refund action.
     *
     * @param array $attr
     * @return array
     */
    private function getInitPaymentRefundAttr($attr)
    {
        $schema = [
            'amount' => '',
        ];

        return $this->processSchemaAttributes($schema, $attr);
    }

    /**
     * Extract header attributes used for payment action.
     *
     * @param array $attr
     * @return array
     */
    private function getInitPaymentHeaderAttr($attr = [])
    {
        if (isset($attr['Authorization'])) {
            $data = array_merge(
                ['Authorization: ' . $this->unifyBearerToken($attr['Authorization'])],
                $this->buildPluginInformationHeader()
            );
        } else {
            $data = $this->buildHeader();
        }

        if (isset($attr['Redirect-URL'])) {
            $data[] = 'Redirect-URL: ' . $attr['Redirect-URL'];
        }

        if (isset($attr['Webhook-URL'])) {
            $data[] = 'Webhook-URL: ' . $attr['Webhook-URL'];
        }

        return $data;
    }

    /**
     * Extract header attributes used for payment data action.
     *
     * @param array $attr
     * @return array
     */
    private function getPaymentHeaderAttr($attr = [])
    {
        $data = $this->buildHeader();

        if (isset($attr['PSU-IP-Address'])) {
            $data[] = 'PSU-IP-Address: ' . $attr['PSU-IP-Address'];
        }

        if (in_array($this->getOption('version'), ['0.2', '0.3'])) {
            if (isset($attr['PSU-IP-Port'])) {
                $data[] = 'PSU-IP-Port: ' . $attr['PSU-IP-Port'];
            }
            if (isset($attr['PSU-User-Agent'])) {
                $data[] = 'PSU-User-Agent: ' . $attr['PSU-User-Agent'];
            }
            if (isset($attr['PSU-Device-ID'])) {
                $data[] = 'PSU-Device-ID: ' . $attr['PSU-Device-ID'];
            }
        }

        return $data;
    }

    /**
     * Extract header attributes used for payment status action.
     *
     * @param array $attr
     * @return array
     */
    private function getPaymentStatusHeaderAttr($attr = [])
    {
        $data = $this->buildHeader();

        if (isset($attr['PSU-IP-Address'])) {
            $data[] = 'PSU-IP-Address: ' . $attr['PSU-IP-Address'];
        }

        if (in_array($this->getOption('version'), ['0.2', '0.3'])) {
            if (isset($attr['PSU-User-Agent'])) {
                $data[] = 'PSU-User-Agent: ' . $attr['PSU-User-Agent'];
            }
            if (isset($attr['PSU-IP-Port'])) {
                $data[] = 'PSU-IP-Port: ' . $attr['PSU-IP-Port'];
            }
            if (isset($attr['PSU-Device-ID'])) {
                $data[] = 'PSU-Device-ID: ' . $attr['PSU-Device-ID'];
            }
        }

        return $data;
    }

    /**
     * Extract header attributes used for init payment refund action.
     *
     * @param array $attr
     * @return array
     */
    private function getInitiatePaymentRefundHeaderAttr($attr = [])
    {
        $data = $this->buildHeader();

        if (isset($attr['Webhook-URL'])) {
            $data[] = 'Webhook-URL: ' . $attr['Webhook-URL'];
        }

        return $data;
    }
}
