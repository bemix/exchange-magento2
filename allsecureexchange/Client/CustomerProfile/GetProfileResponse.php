<?php

namespace allsecureexchange\Client\CustomerProfile;

use allsecureexchange\Client\Json\ResponseObject;

/**
 * Class GetProfileResponse
 *
 * @package allsecureexchange\Client\CustomerProfile
 *
 * @property bool $profileExists
 * @property string $profileGuid
 * @property string $customerIdentification
 * @property string $preferredMethod
 * @property CustomerData $customer
 * @property PaymentInstrument[] $paymentInstruments
 */
class GetProfileResponse extends ResponseObject {

}
