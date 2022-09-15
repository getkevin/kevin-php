<?php

namespace Kevin;

/**
 * Trait providing helper methods used globally.
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
        $data = [
            'Client-Id: '.$this->clientId,
            'Client-Secret: '.$this->clientSecret,
        ];

        return array_merge($data, $this->buildPluginInformationHeader());
    }

    private function buildPluginInformationHeader()
    {
        $data = [];

        $pluginVersion = $this->getOption('pluginVersion');
        $pluginPlatform = $this->getOption('pluginPlatform');
        $pluginPlatformVersion = $this->getOption('pluginPlatformVersion');

        if ($pluginVersion !== null) {
            $data[] = 'Plugin-Version: '.$pluginVersion;
        }

        if ($pluginPlatform !== null) {
            $data[] = 'Plugin-Platform: '.$pluginPlatform;
        }

        if ($pluginPlatformVersion !== null) {
            $data[] = 'Plugin-Platform-Version: '.$pluginPlatformVersion;
        }

        return $data;
    }

    /**
     * Build array with JSON data used in request header.
     *
     * @param array|string $data
     *
     * @return array
     */
    private function buildJsonHeader($data)
    {
        $length = 0;
        if (is_string($data)) {
            $length = strlen($data);
        } elseif (is_array($data)) {
            $length = strlen(json_encode($data));
        }

        return [
            'Content-Type: application/json',
            'Content-Length: '.$length,
        ];
    }

    /**
     * Build default request used for all api calls.
     *
     * @param string $url
     * @param string $type
     * @param string $jsonData
     * @param array  $header
     *
     * @return array
     *
     * @throws KevinException
     */
    private function buildRequest($url, $type, $jsonData, $header)
    {
        $parsed = parse_url($url);

        $host = $parsed['host'];
        $prefix = '';
        $port = 80;

        if ($parsed['scheme'] === 'https') {
            $prefix = 'ssl://';
            $port = 443;
        }

        $port = isset($this->options['port']) ? $this->options['port'] : $port;

        if (preg_match('/(%[0-9A-F]{2})/', $jsonData)) {
            $jsonData = urldecode($jsonData);
        }

        $fp = fsockopen($prefix.$host, $port, $err_no, $err_str, 10);
        if (!$fp) {
            return $this->returnFailure(sprintf('Connection cannot be established to %s', $url));
        }

        $path = $parsed['path'].(isset($parsed['query']) ? '?'.$parsed['query'] : '');

        $default_headers = [
            "$type $path HTTP/1.1",
            "Host: $host",
            'Accept: */*',
            'Accept-Encoding: *',
        ];

        $data = array_merge($default_headers, $header);
        $data[] = 'Connection: Close';
        $data[] = ''; // Separator
        if ($type === 'POST') {
            $data[] = "$jsonData\r\n";
        }

        foreach ($data as $value) {
            fwrite($fp, "$value\r\n");
        }

        $response = '';
        while (!feof($fp)) {
            $response .= fgets($fp, 8192);
        }
        fclose($fp);

        $parts = explode("\r\n\r\n", $response, 2);

        $header = trim($parts[0]);
        $result = trim($parts[1]);

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
            'data' => $result,
        ];
    }

    /**
     * Build default response array.
     *
     * @param array $response
     *
     * @return array
     *
     * @throws KevinException
     */
    private function buildResponse($response)
    {
        $is_error = true;

        switch ($response['code']) {
            case 200:
                $response = json_decode($response['data'], true);
                $is_error = false;
                break;
            case 400:
                $response = json_decode($response['data'], true);
                break;
            case 401:
                $response = ['error' => ['code' => 401, 'name' => 'Unauthorized', 'description' => 'Unauthorized'], 'data' => []];
                break;
            case 500:
                $response = ['error' => ['code' => 500, 'name' => 'Exception', 'description' => 'Internal server error.'], 'data' => []];
                break;
            case 502:
                $response = ['error' => ['code' => 502, 'name' => 'Exception', 'description' => 'Bad Gateway.'], 'data' => []];
                break;
            case 503:
                $response = ['error' => ['code' => 503, 'name' => 'Exception', 'description' => 'Service unavailable.'], 'data' => []];
                break;
            case 504:
                $response = ['error' => ['code' => 504, 'name' => 'Exception', 'description' => 'Gateway timeout.'], 'data' => []];
                break;
            default:
                // Should not happen
                $response = ['error' => ['code' => -1, 'name' => 'Exception', 'description' => 'Unknown error.'], 'data' => []];
        }

        if ($is_error) {
            $error = $response['error'];
            $data = isset($response['data']) ? $response['data'] : null;

            return $this->returnFailure($error['description'], $error['code'], $error['name'], $data);
        }

        return $response;
    }

    /**
     * Process authorization header bearer prefix.
     *
     * @param $token
     *
     * @return string
     */
    private function unifyBearerToken($token)
    {
        $str = 'bearer';
        if (substr(strtolower($token), 0, strlen($str)) === $str) {
            return $token;
        } else {
            return 'Bearer '.$token;
        }
    }

    /**
     * Process string value parameter used in request query or path attributes.
     *
     * @param string $string
     *
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
     * @param array  $parameters
     *
     * @return string
     *
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
     * @return array
     */
    private function processSchemaAttributes(array $schema, array $attr)
    {
        return $this->intersectArrayRecursively($attr, $schema);
    }

    /**
     * Return failure response based on option value.
     *
     * @param string      $message
     * @param int         $code
     * @param string      $name
     * @param string|null $data
     *
     * @return array[]
     *
     * @throws KevinException
     */
    private function returnFailure($message = '', $code = -1, $name = 'Exception', $data = null)
    {
        switch ($this->options['error']) {
            case 'array':
                $response = ['error' => ['code' => $code, 'name' => $name, 'description' => $message], 'data' => $data];
                break;
            default:
                throw new KevinException($message, $code, null, $data);
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
     * @return array
     */
    private function processOptionsAttributes(array $options)
    {
        $data = [
            'error' => 'exception',
            'version' => '0.3',
            'domain' => 'api.kevin.eu',
            'port' => null,
            'scheme' => 'https',
        ];

        $optionError = ['exception', 'array'];
        if (isset($options['error']) && in_array($options['error'], $optionError)) {
            $data['error'] = $options['error'];
        }

        $optionVersion = ['0.1', '0.2', '0.3'];
        if (isset($options['version']) && in_array($options['version'], $optionVersion)) {
            $data['version'] = $options['version'];
        }

        if (isset($options['lang'])) {
            $data['lang'] = $options['lang'];
        }

        $optionDomain = ['api.kevin.eu', 'api-sandbox.kevin.eu', 'api-dev.kevin.eu'];
        if (isset($options['domain']) && in_array($options['domain'], $optionDomain)) {
            $data['domain'] = $options['domain'];
        }

        if (isset($options['pluginVersion'])) {
            $data['pluginVersion'] = $options['pluginVersion'];
        }
        if (isset($options['pluginPlatform'])) {
            $data['pluginPlatform'] = $options['pluginPlatform'];
        }
        if (isset($options['pluginPlatformVersion'])) {
            $data['pluginPlatformVersion'] = $options['pluginPlatformVersion'];
        }

        if (isset($options['port']) && $options['port'] >= 1 && $options['port'] <= 65535) {
            $data['port'] = (int) $options['port'];
        }

        if (isset($options['scheme']) && in_array($options['scheme'], ['https', 'http'], true)) {
            $data['scheme'] = $options['scheme'];
        }

        return $data;
    }

    /**
     * Set up options attribute values.
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

    /**
     * @param string $option
     *
     * @return mixed|null
     */
    private function getOption($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

    /**
     * @return string
     */
    private function getBaseUrl()
    {
        $version = $this->getOption('version');
        $domain = $this->getOption('domain');
        $scheme = $this->getOption('scheme').'://';

        switch ($version) {
            case '0.1':
                $base_url = $scheme.$domain.self::BASE_PATH_V01;
                break;
            case '0.2':
                $base_url = $scheme.$domain.self::BASE_PATH_V02;
                break;
            case '0.3':
                $base_url = $scheme.$domain.self::BASE_PATH_V03;
                break;
            default:
                $base_url = $scheme.$domain.self::BASE_PATH_V03;
        }

        return $base_url;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getEndpointUrl($path = '')
    {
        return $this->getBaseUrl().$path;
    }

    /**
     * @param array $master
     * @param array $mask
     *
     * @return array
     */
    private function intersectArrayRecursively($master, $mask)
    {
        if (!is_array($master)) {
            return $master;
        }

        foreach ($master as $k => $v) {
            if (!isset($mask[$k])) {
                unset($master[$k]);
                continue;
            }
            if (is_array($mask[$k])) {
                $master[$k] = $this->intersectArrayRecursively($master[$k], $mask[$k]);
            }
        }

        return $master;
    }

    /**
     * Appends query param at the end of url.
     *
     * @param string $url
     * @param string $varName
     * @param string $value
     *
     * @return string
     */
    private function appendQueryParam($url, $varName, $value)
    {
        if (empty($url) || empty($varName) || empty($value)) {
            return $url;
        }

        if (strpos($url, '?')) {
            return $url.'&'.$varName.'='.$value;
        } else {
            return $url.'?'.$varName.'='.$value;
        }
    }
}
