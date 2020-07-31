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
     * Client id.
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
     * Options array.
     *
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
     * @param string $url
     * @param string $type
     * @param string $jsonData
     * @param array $header
     * @return array
     * @throws KevinException
     */
    private function buildRequest($url, $type, $jsonData, $header)
    {
        $parsed = parse_url($url);

        $host = $parsed['host'];
        if ($parsed['scheme'] === 'https') {
            $prefix = 'ssl://';
            $port = 443;
        } else {
            $prefix = '';
            $port = 80;
        }

        $fp = fsockopen($prefix . $host, $port, $err_no, $err_str, 10);
        if (!$fp) {

            return $this->returnFailure(sprintf('Connection cannot be established to %s', $url));
        }

        $path = $parsed['path'] . (isset($parsed['query']) ? '?' . $parsed['query'] : '');

        $default_headers = [
            "$type $path HTTP/1.1",
            "Host: $host",
            "Accept: */*",
            "Accept-Encoding: *"
        ];

        $data = array_merge($default_headers, $header);
        $data[] = "Connection: Close";
        $data[] = ""; // Separator

        if ($type === 'POST') {
            $data[] = "$jsonData\r\n";
        }

        foreach ($data as $value) {
            fputs($fp, "$value\r\n");
        }

        $response = '';
        while (!feof($fp)) {
            $response .= fgets($fp, 8192);
        }
        fclose($fp);

        $asd = explode("\r\n\r\n", $response, 2);

        $header = trim($asd[0]);
        $result = trim($asd[1]);

        $header = explode("\r\n", $header);

        $code = -1;
        foreach ($header as $value) {
            if (substr($value, 0, 4) === 'HTTP') {
                preg_match('/(\b[0-9]{3})\b/', $value, $matches);
                $code = $matches[1];
                break;
            }
        }

        return [
            'code' => $code,
            'data' => $result
        ];
    }

    /**
     * Build default response array.
     *
     * @param array $response
     * @return array
     * @throws KevinException
     */
    private function buildResponse($response)
    {
        switch ($response['code']) {
            case 200:
                $response = json_decode($response['data'], true);
                $is_error = false;
                break;
            case 400:
                $response = json_decode($response['data'], true);
                $is_error = true;
                break;
            case 401:
                $response = ['error' => ['code' => -1, 'name' => 'Unauthorized', 'description' => 'Unauthorized'], 'data' => []];
                $is_error = true;
                break;
            default:
                // Should not happen
                $response = ['error' => ['code' => -1, 'name' => 'Exception', 'description' => 'Unknown error.'], 'data' => []];
                $is_error = true;
        }

        if ($is_error) {
            $error = $response['error'];

            return $this->returnFailure($error['description'], $error['code'], $error['name']);
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
     * @param int $code
     * @param string $name
     * @return array[]
     * @throws KevinException
     */
    private function returnFailure($message = '', $code = -1, $name = 'Exception')
    {
        switch ($this->options['error']) {
            case 'exception':
                throw new KevinException($message, $code);

                break;
            case 'array':
                $response = ['error' => ['code' => $code, 'name' => $name, 'description' => $message], 'data' => []];

                break;
            default:
                throw new KevinException($message, $code);
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
