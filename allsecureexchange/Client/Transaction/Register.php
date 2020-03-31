<?php

namespace allsecureexchange\Client\Transaction;

use allsecureexchange\Client\Transaction\Base\AbstractTransaction;
use allsecureexchange\Client\Transaction\Base\AddToCustomerProfileInterface;
use allsecureexchange\Client\Transaction\Base\AddToCustomerProfileTrait;
use allsecureexchange\Client\Transaction\Base\OffsiteInterface;
use allsecureexchange\Client\Transaction\Base\OffsiteTrait;
use allsecureexchange\Client\Transaction\Base\ScheduleInterface;
use allsecureexchange\Client\Transaction\Base\ScheduleTrait;

/**
 * Register: Register the customer's payment data for recurring charges.
 *
 * The registered customer payment data will be available for recurring transaction without user interaction.
 *
 * @package allsecureexchange\Client\Transaction
 */
class Register extends AbstractTransaction implements OffsiteInterface, ScheduleInterface, AddToCustomerProfileInterface {
    use OffsiteTrait;
    use ScheduleTrait;
    use AddToCustomerProfileTrait;
}
