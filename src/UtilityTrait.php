<?php

namespace Kevin;

/**
 * Trait providing helper methods used globally.
 *
 * @package Kevin
 */
trait UtilityTrait
{
    /**
     * Client id
     *
     * @var string
     */
    private $clientId = '';

    /**
     * Client secret.
     *
     * @var string
     */
    private $clientSecret = '';

    /**
     * @var array
     */
    private $options;

    /**
     * Build array with client authentication data used in request header.
     *
     * @return array
     */
    private function buildHeader()
    {
        return [
            'Client-Id: ' . $this->clientId,
            'Client-Secret: ' . $this->clientSecret
        ];
    }

    /**
     * Build array with JSON data used in request header.
     *
     * @param array|string $data
     * @return array
     */
    private function buildJsonHeader($data)
    {
        $length = 0;
        if (is_string($data)) {
            $length = strlen($data);
        } else if (is_array($data)) {
            $length = strlen(json_encode($data));
        }

        return [
            'Content-Type: application/json',
            'Content-Length: ' . $length
        ];
    }

    /**
     * Build default request used for all api calls.
     *
     * @param $url
     * @param $type
     * @param $data
     * @param $header
     * @return array
     * @throws KevinException
     */
    private function buildRequest($url, $type, $data, $header)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        switch ($type) {
            case 'GET';
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                break;
            case 'POST';
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

        $result = curl_exec($ch);

        if (!$result) {

            return $this->returnFailure('Connection failure.');
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return [
            'code' => $code,
            'data' => $result
        ];
    }

    /**
     * Build default response array.
     *
     * @param $response
     * @return array
     */
    private function buildResponse($response)
    {
        switch ($response['code']) {
            case 200:
            case 400:
                $response = json_decode($response['data'], true);
                break;
            case 401:
                $response = ['error' => ['code' => -1, 'name' => 'Unauthorized', 'description' => 'Unauthorized'], 'data' => []];
                break;
            default:
                // Should not happen
                $response = [];
        }

        return $response;
    }

    /**
     * Process authorization header bearer prefix.
     *
     * @param $token
     * @return string
     */
    private function unifyBearerToken($token)
    {
        $str = 'bearer';
        if (substr(strtolower($token), 0, strlen($str)) === $str) {

            return $token;
        } else {

            return 'Bearer ' . $token;
        }
    }

    /**
     * Process string value parameter used in request query or path attributes.
     *
     * @param string $string
     * @return string
     */
    private function escParam($string = '')
    {
        return $string = urlencode(trim($string));
    }

    /**
     * Process url parameters and glue them into path.
     *
     * @param string $url
     * @param array $parameters
     * @return string
     * @throws KevinException
     */
    private function gluePath($url, ...$parameters)
    {
        $pattern = '/\{.*?\}/';

        $matched = preg_match_all($pattern, $url);
        if ($matched !== count($parameters)) {

            throw new KevinException('Parameter mismatch.');
        }

        foreach ($parameters as $parameter) {
            $url = preg_replace($pattern, $this->escParam($parameter), $url, 1);
        }

        return $url;
    }

    /**
     * Process and set up values based on supplied schema array.
     *
     * @param array $schema
     * @param array $attr
     * @return array
     */
    private function processSchemaAttributes(array $schema, array $attr)
    {
        $data = [];

        foreach ($schema as $item => $value) {
            if (is_string($value)) {
                if (isset($attr[$value])) {
                    $data[$value] = strval($attr[$value]);
                }
            } else if (is_array($value)) {
                foreach ($value as $sub_value) {
                    if (isset($attr[$item][$sub_value])) {
                        $data[$item] = [];
                        $data[$item][$sub_value] = strval($attr[$item][$sub_value]);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Return failure response based on option value.
     *
     * @param string $message
     * @return array[]
     * @throws KevinException
     */
    private function returnFailure($message = '')
    {
        switch ($this->options['error']) {
            case 'exception':
                throw new KevinException($message);

                break;
            case 'array':
                $response = ['error' => ['code' => -1, 'name' => 'Exception', 'description' => $message], 'data' => []];

                break;
            default:
                throw new KevinException($message);
        }

        return $response;
    }

    /**
     * Set up client credentials parameters.
     *
     * @param string $clientId
     * @param string $clientSecret
     */
    private function setClientCredentials($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Process options attribute values.
     *
     * @param array $options
     * @return array
     */
    private function processOptionsAttributes(array $options)
    {
        $data = ['error' => 'exception'];

        $option_error = ['exception', 'array'];
        if (isset($options['error']) && in_array($options['error'], $option_error)) {
            $data['error'] = $options['error'];
        }

        return $data;
    }

    /**
     * Set up options attribute values.
     *
     * @param array $options
     */
    private function setOptionsAttributes(array $options)
    {
        $this->options = $this->processOptionsAttributes($options);
    }

    /**
     * Check library requirements and compatibility.
     *
     * @throws KevinException
     */
    private function initialize()
    {
        if (!function_exists('curl_version')) {

            throw new KevinException('CURL is not enabled.');
        }

        if (!strlen($this->clientId) || !strlen($this->clientSecret)) {

            throw new KevinException('ClientID and ClientSecret are required.');
        }
    }
}
