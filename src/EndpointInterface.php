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

    /**
     * List of Auth related endpoints.
     */

    const URL_COUNTRIES = self::BASE_URL . '/auth/countries';
    const URL_BANKS = self::BASE_URL . '/auth/banks';
    const URL_BANK = self::BASE_URL . '/auth/banks/{bankId}';
    const URL_AUTH = self::BASE_URL . '/auth';
    const URL_RECEIVE_TOKEN = self::BASE_URL . '/auth/token';
    const URL_REFRESH_TOKEN = self::BASE_URL . '/auth/token';
    const URL_TOKEN_CONTENT = self::BASE_URL . '/auth/token/content';

    /**
     * List of Payment related endpoints.
     */

    const URL_INIT_PAYMENT = self::BASE_URL . '/pis/payment';
    const URL_PAYMENT = self::BASE_URL . '/pis/payment/{paymentId}';
    const URL_PAYMENT_STATUS = self::BASE_URL . '/pis/payment/{paymentId}/status';
}
