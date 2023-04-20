
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'allsecureexchange',
                component: 'Allsecureexchange_Allsecureexchange/js/allsecureexchange'
            }
        );
        return Component.extend({});
    }
);