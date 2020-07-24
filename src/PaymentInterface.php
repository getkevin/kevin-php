<?php

namespace Kevin;

/**
 * Interface to provide base list of payment related methods.
 */
interface PaymentInterface
{
    public function initPayment($attr = []);

    public function getPayment($paymentId, $attr = []);

    public function getPaymentStatus($paymentId, $attr = []);
}
