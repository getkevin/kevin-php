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
            'creditorName',
            'creditorAccount' => [
                'iban',
                'bban',
                'sortCodeAccountNumber'
            ],
            'debtorAccount' => [
                'iban',
                'bban',
                'sortCodeAccountNumber'
            ],
            'amount',
            'currencyCode',
            'endToEndId',
            'informationUnstructured',
            'requestedExecutionDate',
            'identifier' => [
                'email'
            ]
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
        $data = [];

        if (isset($attr['Authorization'])) {
            $data[] = 'Authorization: ' . $this->unifyBearerToken($attr['Authorization']);
        } else {
            $data = array_merge($this->buildHeader(), $data);
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

        return $data;
    }
}
