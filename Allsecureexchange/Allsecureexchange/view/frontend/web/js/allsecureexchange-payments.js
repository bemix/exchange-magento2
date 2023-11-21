
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
            ,{
                type: 'allsecureexchange_sofort',
                component: 'Allsecureexchange_Allsecureexchange/js/allsecureexchange_additional'
            }
        );
        return Component.extend({});
    }
);