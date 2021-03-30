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
    const BASE_URL_V03 = self::BASE_URL . '/v0.3';

    /**
     * List of Auth related endpoints.
     */

    const PATH_COUNTRIES = '/auth/countries';
    const PATH_BANKS = '/auth/banks';
    const PATH_PAYMENT_METHODS = '/auth/paymentMethods';
    const PATH_BANK = '/auth/banks/{bankId}';
    const PATH_BANK_BY_CARD_NUMBER_PIECE = '/auth/banks/cards/{cardNumberPiece}';
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
    const PATH_INITIATE_PAYMENT_REFUND = '/pis/payment/{paymentId}/refunds';
    const PATH_GET_PAYMENT_REFUNDS = '/pis/payment/{paymentId}/refunds';
}
