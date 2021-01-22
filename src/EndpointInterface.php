<?php

namespace Kevin;

/**
 * Interface to provide base list of supported endpoints.
 */
interface EndpointInterface
{
    /**
     * Base URL used for sending API calls.
     */
    const BASE_URL = 'https://api.getkevin.eu/platform';
    const BASE_URL_V01 = self::BASE_URL . '/v0.1';
    const BASE_URL_V02 = self::BASE_URL . '/v0.2';

    /**
     * List of Auth related endpoints.
     */

    const PATH_COUNTRIES = '/auth/countries';
    const PATH_BANKS = '/auth/banks';
    const PATH_BANK = '/auth/banks/{bankId}';
    const PATH_AUTH = '/auth';
    const PATH_RECEIVE_TOKEN = '/auth/token';
    const PATH_REFRESH_TOKEN = '/auth/token';
    const PATH_TOKEN_CONTENT = '/auth/token/content';

    /**
     * List of Payment related endpoints.
     */

    const PATH_INIT_PAYMENT = '/pis/payment';
    const PATH_PAYMENT = '/pis/payment/{paymentId}';
    const PATH_PAYMENT_STATUS = '/pis/payment/{paymentId}/status';
}
