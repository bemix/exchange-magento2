<?php

namespace Allsecureexchange\Client\Transaction;

use Allsecureexchange\Client\Transaction\Base\AbstractTransaction;
use Allsecureexchange\Client\Transaction\Base\AddToCustomerProfileInterface;
use Allsecureexchange\Client\Transaction\Base\AddToCustomerProfileTrait;
use Allsecureexchange\Client\Transaction\Base\OffsiteInterface;
use Allsecureexchange\Client\Transaction\Base\OffsiteTrait;
use Allsecureexchange\Client\Transaction\Base\ScheduleInterface;
use Allsecureexchange\Client\Transaction\Base\ScheduleTrait;

/**
 * Register: Register the customer's payment data for recurring charges.
 *
 * The registered customer payment data will be available for recurring transaction without user interaction.
 *
 * @package Allsecureexchange\Client\Transaction
 */
class Register extends AbstractTransaction implements OffsiteInterface, ScheduleInterface, AddToCustomerProfileInterface {
    use OffsiteTrait;
    use ScheduleTrait;
    use AddToCustomerProfileTrait;
}
