<?php

namespace allsecureexchange\Client\Transaction;

use allsecureexchange\Client\Transaction\Base\AbstractTransactionWithReference;
use allsecureexchange\Client\Transaction\Base\AmountableInterface;
use allsecureexchange\Client\Transaction\Base\AmountableTrait;
use allsecureexchange\Client\Transaction\Base\ItemsInterface;
use allsecureexchange\Client\Transaction\Base\ItemsTrait;

/**
 * Payout: Payout a certain amount of money to the customer. (Debits the merchant's account, Credits the customer's account)
 *
 * @package allsecureexchange\Client\Transaction
 */
class Payout extends AbstractTransactionWithReference implements AmountableInterface, ItemsInterface {
    use ItemsTrait;
    use AmountableTrait;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $callbackUrl;

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCallbackUrl() {
        return $this->callbackUrl;
    }

    /**
     * @param string $callbackUrl
     */
    public function setCallbackUrl($callbackUrl) {
        $this->callbackUrl = $callbackUrl;
    }

}
