<?php

namespace Kevin;

class SecurityManager
{
    /**
     * An implementation of kevin.'s signature.
     *
     * @see https://docs.kevin.eu/public/platform/v0.3#tag/Signature
     *
     * @param string   $endpointSecret
     * @param string   $requestBody
     * @param array    $headers
     * @param string   $webhookUrl
     * @param int|null $timestampTimeout in milliseconds
     *
     * @return bool
     */
    public static function verifySignature($endpointSecret, $requestBody, $headers, $webhookUrl, $timestampTimeout = null)
    {
        $headers = array_change_key_case($headers);

        if (!self::verifyTimeout($timestampTimeout, $headers)) {
            return false;
        }

        $data = 'POST'.$webhookUrl.$headers['x-kevin-timestamp'].$requestBody;
        $signature = hash_hmac('sha256', $data, $endpointSecret);

        return $signature === $headers['x-kevin-signature'];
    }

    /**
     * Verify timestamp timeout in milliseconds.
     *
     * @param int|null $timestampTimeout
     * @param array    $headers
     *
     * @return bool
     */
    private static function verifyTimeout($timestampTimeout, $headers)
    {
        if (!isset($headers['x-kevin-timestamp'])) {
            return false;
        }

        if ($timestampTimeout === null) {
            return true;
        }

        $timeDifference = (time() * 1000) - $headers['x-kevin-timestamp'];

        return $timestampTimeout > $timeDifference;
    }
}
