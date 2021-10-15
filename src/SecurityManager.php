<?php

namespace Kevin;

class SecurityManager
{
    /**
     * Endpoint secret.
     *
     * @var string
     */
    private $endpointSecret;

    /**
     * SecurityManager constructor.
     *
     * @param string $endpointSecret
     */
    public function __construct($endpointSecret = '')
    {
        $this->endpointSecret = $endpointSecret;
    }

    /**
     * An implementation of kevin.'s signature.
     * @see https://docs.kevin.eu/public/platform/v0.3#tag/Signature
     *
     * @param string $requestBody
     * @param array $headers
     * @param string $webhookUrl
     * @param float $timestampTimeout in milliseconds
     * @return bool
     */
    public function verifySignature($requestBody, $headers, $webhookUrl, $timestampTimeout = null)
    {
        if (!$this->verifyTimeout($timestampTimeout, $headers)) {
            return false;
        }

        $data = 'POST' . $webhookUrl . $headers['X-Kevin-Timestamp'] . $requestBody;
        $signature = $this->generateHasWithHmac256($data, $this->endpointSecret);

        return $signature == $headers['X-Kevin-Signature'];
    }

    /**
     * Generate HMAC SHA256 hash.
     *
     * @param string $data
     * @param string $key
     * @return string
     */
    private function generateHasWithHmac256($data, $key)
    {
        return hash_hmac('sha256', $data, $key);
    }

    /**
     * Verify timestamp timeout in milliseconds.
     *
     * @param float $timestampTimeout
     * @param array $headers
     * @return bool
     */
    private function verifyTimeout($timestampTimeout, $headers)
    {
        if (!isset($timestampTimeout) || !isset($headers['X-Kevin-Timestamp'])) {
            return true;
        }

        $timeDifference = (microtime(true) * 1000) - $headers['X-Kevin-Timestamp'];

        return $timestampTimeout > $timeDifference;
    }
}
