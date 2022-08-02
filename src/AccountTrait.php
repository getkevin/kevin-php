<?php

namespace Kevin;

/**
 * Trait providing helper methods used for accounts.
 */
trait AccountTrait
{
    use UtilityTrait;

    /**
     * Extract query attributes used for account transaction action.
     *
     * @param array $attr
     *
     * @return array
     */
    private function getAccountTransactionsQueryAttr($attr = [])
    {
        $data = [];

        if (isset($attr['dateFrom'])) {
            $data['dateFrom'] = (string) $attr['dateFrom'];
        }

        if (isset($attr['dateTo'])) {
            $data['dateTo'] = (string) $attr['dateTo'];
        }

        return $data;
    }

    /**
     * Extract header attributes used for account data action.
     *
     * @param array $attr
     *
     * @return array
     */
    private function getAccountHeaderAttr($attr = [])
    {
        $data = $this->buildHeader();

        if (isset($attr['Authorization'])) {
            $data = array_merge(
                $data,
                ['Authorization: '.$this->unifyBearerToken($attr['Authorization'])]
            );
        }

        if (isset($attr['PSU-IP-Address'])) {
            $data[] = 'PSU-IP-Address: '.$attr['PSU-IP-Address'];
        }
        if (isset($attr['PSU-IP-Port'])) {
            $data[] = 'PSU-IP-Port: '.$attr['PSU-IP-Port'];
        }
        if (isset($attr['PSU-User-Agent'])) {
            $data[] = 'PSU-User-Agent: '.$attr['PSU-User-Agent'];
        }
        if (isset($attr['PSU-Http-Method'])) {
            $data[] = 'PSU-Http-Method: '.$attr['PSU-Http-Method'];
        }

        if (in_array($this->getOption('version'), ['0.2', '0.3'])) {
            if (isset($attr['PSU-Device-ID'])) {
                $data[] = 'PSU-Device-ID: '.$attr['PSU-Device-ID'];
            }
        }

        return $data;
    }
}
