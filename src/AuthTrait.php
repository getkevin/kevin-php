<?php

namespace Kevin;

/**
 * Trait providing helper methods used for authentication.
 */
trait AuthTrait
{
    use UtilityTrait;

    /**
     * Extract query attributes used for bank action.
     *
     * @param array $attr
     *
     * @return array
     */
    private function getBankQueryAttr($attr = [])
    {
        $data = [];

        if (isset($attr['countryCode'])) {
            $data['countryCode'] = (string) $attr['countryCode'];
        }

        return $data;
    }

    /**
     * Extract query attributes used for authentication action.
     *
     * @param array $attr
     *
     * @return array
     */
    private function getAuthQueryAttr($attr = [])
    {
        $data = [];

        if (isset($attr['bankId'])) {
            $data['bankId'] = (string) $attr['bankId'];
        }

        if (isset($attr['redirectPreferred'])) {
            $data['redirectPreferred'] = (bool) (filter_var($attr['redirectPreferred'], \FILTER_VALIDATE_BOOLEAN)) ? 'true' : 'false';
        }

        if (isset($attr['scopes'])) {
            $data['scopes'] = explode(',', $attr['scopes']);
        }

        return $data;
    }

    /**
     * Extract body attributes used for authentication action.
     *
     * @param array $attr
     *
     * @return array
     */
    private function getAuthBodyAttr($attr = [])
    {
        $data = [];

        if (isset($attr['email'])) {
            $data['email'] = (string) $attr['email'];
        }

        if (isset($attr['cardMethod'])) {
            $data['cardMethod'] = [];
        }

        return $data;
    }

    /**
     * Extract header attributes used for authentication action.
     *
     * @param array $attr
     *
     * @return array
     */
    private function getAuthHeaderAttr($attr = [])
    {
        $data = $this->buildHeader();

        if (isset($attr['Request-Id'])) {
            $data[] = 'Request-Id: '.$attr['Request-Id'];
        }

        if (isset($attr['Redirect-URL'])) {
            $data[] = 'Redirect-URL: '.$attr['Redirect-URL'];
        }

        if ($this->getOption('version') == '0.3') {
            if (isset($attr['Webhook-URL'])) {
                $data[] = 'Webhook-URL: '.$attr['Webhook-URL'];
            }
        }

        return $data;
    }

    /**
     * Extract body attributes used for access token action.
     *
     * @param array $attr
     *
     * @return array
     */
    private function getReceiveTokenBodyAttr($attr = [])
    {
        $data = ['grantType' => 'authorizationCode'];

        if (is_string($attr)) {
            $data['code'] = $attr;
        } else {
            if (isset($attr['code'])) {
                $data['code'] = (string) $attr['code'];
            }
        }

        return $data;
    }

    /**
     * Extract body attributes used for refresh token action.
     *
     * @param array $attr
     *
     * @return array
     */
    private function getRefreshTokenBodyAttr($attr = [])
    {
        $data = ['grantType' => 'refreshToken'];

        if (is_string($attr)) {
            $data['refreshToken'] = $attr;
        } else {
            if (isset($attr['refreshToken'])) {
                $data['refreshToken'] = (string) $attr['refreshToken'];
            }
        }

        return $data;
    }

    /**
     * Extract header attributes used for token content action.
     *
     * @param array $attr
     *
     * @return array|string
     */
    private function getReceiveTokenContentHeaderAttr($attr = [])
    {
        $data = $this->buildHeader();

        if (is_string($attr)) {
            $data[] = 'Authorization: '.$this->unifyBearerToken($attr);
        } else {
            if (isset($attr['Authorization'])) {
                $data[] = 'Authorization: '.$this->unifyBearerToken($attr['Authorization']);
            }
        }

        return $data;
    }
}
