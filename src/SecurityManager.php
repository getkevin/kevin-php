<?php

namespace Kevin;

class SecurityManager
{
    /**
     * An implementation of kevin.'s signature.
     * @see https://docs.kevin.eu/public/platform/v0.3#tag/Signature
     *
     * @param string $endpointSecret
     * @param string $requestBody
     * @param array $headers
     * @param string $webhookUrl
     * @param int|null $timestampTimeout in milliseconds
     * @return bool
     */
    public static function verifySignature($endpointSecret, $requestBody, $headers, $webhookUrl, $timestampTimeout = null)
    {
        if (!self::verifyTimeout($timestampTimeout, $headers)) {
            return false;
        }

        print_r("good\n");

        $data = 'POST' . $webhookUrl . $headers['X-Kevin-Timestamp'] . $requestBody;
        $signature = hash_hmac('sha256', $data, $endpointSecret);

        return $signature === $headers['X-Kevin-Signature'];
    }

    /**
     * Verify timestamp timeout in milliseconds.
     *
     * @param int|null $timestampTimeout
     * @param array $headers
     * @return bool
     */
    private static function verifyTimeout($timestampTimeout, $headers)
    {
        if (!isset($headers['X-Kevin-Timestamp'])) {
            return false;
        }

        if ($timestampTimeout === null) {
            return true;
        }

        $timeDifference = (time() * 1000) - $headers['X-Kevin-Timestamp'];

        return $timestampTimeout > $timeDifference;
    }
}
