<?php

namespace allsecureexchange\Client\Transaction;

use allsecureexchange\Client\Transaction\Base\AbstractTransactionWithReference;
use allsecureexchange\Client\Transaction\Base\AmountableInterface;
use allsecureexchange\Client\Transaction\Base\AmountableTrait;
use allsecureexchange\Client\Transaction\Base\ItemsInterface;
use allsecureexchange\Client\Transaction\Base\ItemsTrait;

/**
 * Capture: Charge a previously preauthorized transaction.
 *
 * @package allsecureexchange\Client\Transaction
 */
class Capture extends AbstractTransactionWithReference implements AmountableInterface, ItemsInterface {
    use AmountableTrait;
    use ItemsTrait;
}
