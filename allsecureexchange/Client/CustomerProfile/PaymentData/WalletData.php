<?php

namespace allsecureexchange\Client\CustomerProfile\PaymentData;

/**
 * Class WalletData
 *
 * @package allsecureexchange\Client\CustomerProfile\PaymentData
 *
 * @property string $walletReferenceId
 * @property string $walletOwner
 * @property string $walletType
 */
class WalletData extends PaymentData {

    const TYPE_PAYPAL = 'paypal';

}
