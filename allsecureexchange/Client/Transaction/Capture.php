<?php

namespace Allsecureexchange\Client\Transaction;

use Allsecureexchange\Client\Transaction\Base\AbstractTransactionWithReference;
use Allsecureexchange\Client\Transaction\Base\AmountableInterface;
use Allsecureexchange\Client\Transaction\Base\AmountableTrait;
use Allsecureexchange\Client\Transaction\Base\ItemsInterface;
use Allsecureexchange\Client\Transaction\Base\ItemsTrait;

/**
 * Capture: Charge a previously preauthorized transaction.
 *
 * @package Allsecureexchange\Client\Transaction
 */
class Capture extends AbstractTransactionWithReference implements AmountableInterface, ItemsInterface {
    use AmountableTrait;
    use ItemsTrait;
}
