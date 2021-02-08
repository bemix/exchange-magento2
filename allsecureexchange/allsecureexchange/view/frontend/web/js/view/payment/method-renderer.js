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
                type: 'allsecureexchange_creditcard',
                component: 'Allsecureexchange_Allsecureexchange/js/view/payment/method-renderer/creditcard'
            },
        );

        return Component.extend({});
    }
);
