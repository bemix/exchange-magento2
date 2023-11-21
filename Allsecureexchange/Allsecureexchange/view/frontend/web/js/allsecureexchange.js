/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/action/place-order',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Ui/js/model/messageList',
], function ($,
        Component,
        placeOrderAction,
        additionalValidators,
        messageList,
        ) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Allsecureexchange_Allsecureexchange/allsecureexchange',
            isCardNumberValid: false,
            isCardCvvValid: false,
            isCardTypesAllowedValid: false,
            isValidBrand: false,
            isPaymentJsEnabled: false,
            allsecurepayPayment: null,
            enable_installment: false,
        },
        initialize: function () {
            this._super();
            
            var self = this;
            
            if (window.checkoutConfig.payment.allsecureexchange.enable_installment) {
                self.enable_installment = true;
            }
 
            if (window.checkoutConfig.payment.allsecureexchange.checkout_mode == 'paymentjs') {
                self.isPaymentJsEnabled = true;
                
                var public_integration_key = window.checkoutConfig.payment.allsecureexchange.integration_key;

                self.allsecurepayPayment = new PaymentJs();
                self.allsecurepayPayment.init(public_integration_key, 'allsecurepay_cc_number', 'allsecurepay_cc_cvv', function(payment) {
                    var numberStyle = {
                        'width': '225px',
                        'background': '#ffffff',
                        'background-clip': 'padding-box',
                        'border': '1px solid #c2c2c2',
                        'border-radius': '1px',
                        'font-size': '14px',
                        'height': '32px',
                        'line-height': '1.42857143',
                        'padding': '0 9px',
                        'vertical-align': 'baseline',
                        'box-sizing': 'border-box',
                    };
                    var cvvStyle = {
                        'width': '55px',
                        'background': '#ffffff',
                        'background-clip': 'padding-box',
                        'border': '1px solid #c2c2c2',
                        'border-radius': '1px',
                        'font-size': '14px',
                        'height': '32px',
                        'line-height': '1.42857143',
                        'padding': '0 9px',
                        'vertical-align': 'baseline',
                        'box-sizing': 'border-box',
                    };

                    // Set the initial style
                    self.allsecurepayPayment.setNumberStyle(numberStyle);
                    self.allsecurepayPayment.setCvvStyle(cvvStyle);

                    self.allsecurepayPayment.numberOn('input', function(data) {
                        self.installmentHandler(data);
                        if (data.validNumber) {
                            self.isCardNumberValid = true;
                        } else {
                            self.isCardNumberValid = false;
                        }

                        if (self.getCardSupported().includes(data.cardType)) {
                            self.isValidBrand = true;
                        } else {
                            self.isValidBrand = false;
                        }
                    })

                    self.allsecurepayPayment.cvvOn('input', function(data) {
                        if (data.validCvv) {
                            self.isCardCvvValid = true;
                        } else {
                            self.isCardCvvValid = false;
                        }
                    })
                });

                $(document).keyup('#allsecurepay_expiration_date', function () {
                    self.allsecurepayExpiryField();
                });
                
                $(document).blur('#allsecurepay_expiration_date', function () {
                    self.allsecurepayValidateExpiry();
                });
                
                
                $(document).click('#allsecurepay_pay_installment', function () {
                    if ($('#allsecurepay_pay_installment').is(':checked')) {
                        $('#allsecurepay_installment_number_container').show();
                    } else {
                         $('#allsecurepay_installment_number_container').hide();
                    }
                });
            }
            
            return this;
        },
        installmentHandler: function (data) {
            var self = this;
            if ($('#allsecurepay_pay_installment_container').length > 0) {
                var isInstallmentAllowed = self.getBins().includes(data.firstSix);
                if (isInstallmentAllowed) {
                    var cardBin = data.firstSix;
                    $('#allsecurepay_pay_installment_container').show();
                    self.updateInstallmentNumbers(cardBin);
                } else {
                    $('#allsecurepay_pay_installment_container').hide();
                    $('#allsecurepay_installment_number_container').hide();
                    $('#allsecurepay_pay_installment').prop('checked', false);
                    $('#allsecurepay_installment_number').html('');
                }
            }
        },
        updateInstallmentNumbers: function (cardBin) {
            $('#allsecurepay_installment_number').html('');
            var installment_numbers = window.checkoutConfig.payment.allsecureexchange.allowed_installments[cardBin];
            if (installment_numbers.length > 0) {
                installment_numbers = installment_numbers.toString().split(",");
                var optionText = '';
                installment_numbers.forEach(function (installment_number) {
                    installment_number = installment_number.trim();
                    if (installment_number.length == 1) {
                        installment_number = '0'+installment_number;
                    }
                    var option = '<option value="'+installment_number+'">'+installment_number+'</option>';
                    optionText += option;
                });
                $('#allsecurepay_installment_number').html(optionText);
            }
        },
        getInstructions: function () {
            return window.checkoutConfig.payment.instructions[this.item.method];
        },
        getImageUrl: function (image) {
            return window.checkoutConfig.payment.allsecureexchange.image_path+'/'+image;
        },
        getCardSupported: function() {
            var card_supported = window.checkoutConfig.payment.allsecureexchange.card_supported;
            return card_supported;
        },
        getBins: function() {
            var bins = window.checkoutConfig.payment.allsecureexchange.installment_bins;
            return bins;
        },
        getExpiryMonths: function() {
            return window.checkoutConfig.payment.allsecureexchange.months;
        },
        getExpiryYears: function() {
            return window.checkoutConfig.payment.allsecureexchange.years;
        },
        getExpiryMonthsValues: function () {
            return _.map(this.getExpiryMonths(), function (value, key) {
                if (key == 0) {
                    key = '';
                }
                return {
                    'value': key,
                    'month': value
                };
            });
        },
        getExpiryYearsValues: function () {
            return _.map(this.getExpiryYears(), function (value, key) {
                if (key == 0) {
                    key = '';
                }
                return {
                    'value': key,
                    'year': value
                };
            });
        },
        allsecurepayExpiryField: function() {
            if (event.target.id == 'allsecurepay_expiration_date') {
                String.fromCharCode(event.keyCode);
                var a = event.keyCode; - 1 === [8].indexOf(a) && (event.target.value = event.target.value.replace(/^([1-9]\/|[2-9])$/g, "0$1/").replace(/^(0[1-9]|1[0-2])$/g, "$1/").replace(/^([0-1])([3-9])$/g, "0$1/$2").replace(/^(0?[1-9]|1[0-2])([0-9]{2})$/g, "$1/$2").replace(/^([0]+)\/|[0]+$/g, "0").replace(/[^\d\/]|^[\/]*$/g, "").replace(/\/\//g, "/"));
            }
        },
        allsecurepayValidateExpiry: function () {
            var expiryDate = $("#allsecurepay_expiration_date").val();
            expiryDate = expiryDate.split("/");
            var month = expiryDate[0];
            var year = expiryDate[1];
            $("#allsecurepay_expiration_month").val(month);
            $("#allsecurepay_expiration_year").val(20 + year);
        },
        validateCardData: function () {
            var valid = true;
            
            var isCardHolderNameValid = this.validateCardHolderName();
            var isCardExpirationDateValid = this.validateCardExpirationDate();
            var isCardSecureDataValid = this.validateCardSecureData(); 
            
            if (!(isCardHolderNameValid && isCardExpirationDateValid && isCardSecureDataValid)) {
                valid = false;
            }
            
            return valid;
        },
        validateCardHolderName: function() {
            var valid = true;
            var cardHolderNameRegex = /^[a-z ,.'-]+$/i;
            
            $('#allsecurepay_cc_name-required-error').hide();
            $('#allsecurepay_cc_name-invalid-error').hide();
            
            $('#allsecurepay_expiration-required-error').hide();
            $('#allsecurepay_expiration-invalid-error').hide();
            $('#allsecurepay_expiration_year-error').hide();
            
            $('#allsecurepay_cc_number-error').hide();
            $('#allsecurepay_cc_cvv-error').hide();
            $('#allsecurepay_cc_number-not-supported-error').hide();
            
            var cardHolderName = $("#allsecurepay_cc_name").val();
            if(cardHolderName == "") {
                $('#allsecurepay_cc_name-required-error').show()
                valid = false;
            } else if(!cardHolderNameRegex.test(cardHolderName)) {
                $('#allsecurepay_cc_name-invalid-error').show()
                valid = false;
            }
            
            return valid;
        },
        validateCardExpirationDate: function() {
            var valid = true;
            
            this.allsecurepayValidateExpiry();

            $('#allsecurepay_expiration-required-error').hide();
            $('#allsecurepay_expiration-invalid-error').hide();
            $('#allsecurepay_expiration_year-error').hide();
            
            var cardExpiryDate = $("#allsecurepay_expiration_date").val();
            var cardExpiryMonth = $("#allsecurepay_expiration_month").val();
            var cardExpiryYear = $("#allsecurepay_expiration_year").val();
            
            if(cardExpiryDate === "") {
                $('#allsecurepay_expiration-required-error').show();
                valid = false;
            } else {
                if(cardExpiryMonth === "") {
                    $('#allsecurepay_expiration-invalid-error').show();
                    valid = false;
                } else if(cardExpiryYear === "") {
                    $('#allsecurepay_expiration-invalid-error').show();
                    valid = false;
                }
            }
            
            if (valid) {
                var minMonth = new Date().getMonth() + 1;
                var minYear = new Date().getFullYear();
                var month = parseInt(cardExpiryMonth, 10);
                var year = parseInt(cardExpiryYear, 10);
                
                if ( !( 
                        (year > minYear) || 
                        ((year === minYear) && (month >= minMonth)) 
                      )
                ) {
                    $('#allsecurepay_expiration-invalid-error').show();
                    valid = false;
                }
            }
            
            return valid;
        },
        validateCardSecureData: function() {
            var valid = true;
            
            $('#allsecurepay_cc_number-error').hide();
            $('#allsecurepay_cc_cvv-error').hide();
            $('#allsecurepay_cc_number-not-supported-error').hide();
            
            if (!this.isCardNumberValid) {
                $('#allsecurepay_cc_number-error').show();
                valid = false;
            } else if (!this.isValidBrand) {
                $('#allsecurepay_cc_number-not-supported-error').show();
                valid = false;
            }
            if (!this.isCardCvvValid) {
                $('#allsecurepay_cc_cvv-error').show();
                valid = false;
            }
            
            return valid;
        },
        allsecurepayTokenize: function() {
            var self = this;
            var cardHolderName = $("#allsecurepay_cc_name").val();
            var cardExpiryMonth = $("#allsecurepay_expiration_month").val();
            var cardExpiryYear = $("#allsecurepay_expiration_year").val();
            
            var cardRequestData = {
                card_holder: cardHolderName,
                month: cardExpiryMonth,
                year: cardExpiryYear
            };

            self.allsecurepayPayment.tokenize(
                cardRequestData,
                function (token, cardData) {
                    $('#allsecurepay_transaction_token').val(token);
                    return true;
                },
                function (errors) {
                    self.handleErrors(errors);
                    return false;
                }
            );
        },
        handleErrors: function() {
            $.each(errors, function(key, value) {
                var errorattribute = value.attribute;
                var errorkey = value.key;
                var errormessage = value.message;
                
                if (errorattribute == 'integration_key') {
                    messageList.addErrorMessage({message: errormessage});
                } else if (errorattribute == 'number') {
                    $('#allsecurepay_cc_number-error').show();
                } else if (errorattribute ==  'cvv' ) {
                    $('#allsecurepay_cc_cvv-error').show();
                } else if (errorattribute == 'card_holder') {
                    if (errorkey == 'errors.blank') {
                        $('#allsecurepay_cc_name-required-error').show();
                    } else {
                        $('#allsecurepay_cc_name-invalid-error').show();
                    }
                } else if (errorattribute == 'month') {
                    if (errorkey == 'errors.blank') {
                        $('#allsecurepay_expiration-required-error').show();
                    } else {
                        $('#allsecurepay_expiration-invalid-error').show();
                    }
                } else if (errorattribute == 'year') {
                    if (errorkey == 'errors.blank') {
                        $('#allsecurepay_expiration_year-error').show();
                    } else {
                        $('#allsecurepay_expiration-invalid-error').show();
                    }
                }
            });  
        },
        isInstallmentAvailed: function() {
            var self = this;
            var availed = 0;
            if (self.enable_installment && $('#allsecurepay_pay_installment').is(':checked')) {
                var availed = 1;
            }
            return availed;
        },
        getInstallmentNumber: function() {
            var self = this;
            var installment_number = '';
            if (self.enable_installment) {
                installment_number = $('#allsecurepay_installment_number').val();
            }
            return installment_number;
            
        },
        getData: function () {
            var data = {
                'method': this.getCode(),
                'additional_data': {
                    'allsecurepay_transaction_token': $('#allsecurepay_transaction_token').val(),
                    'allsecurepay_pay_installment': this.isInstallmentAvailed(),
                    'allsecurepay_installment_number': this.getInstallmentNumber()
                }
            };
            return data;
        },
        placeOrder: function (data, event) {
            if (event) {
                event.preventDefault();
            }
            var self = this,
                    placeOrder;
            
            if (this.isPaymentJsEnabled) {
                if (this.validateCardData() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);

                    var cardHolderName = $("#allsecurepay_cc_name").val();
                    var cardExpiryMonth = $("#allsecurepay_expiration_month").val();
                    var cardExpiryYear = $("#allsecurepay_expiration_year").val();

                    var cardRequestData = {
                        card_holder: cardHolderName,
                        month: cardExpiryMonth,
                        year: cardExpiryYear
                    };

                    self.allsecurepayPayment.tokenize(
                        cardRequestData,
                        function (token, cardData) {
                            $('#allsecurepay_transaction_token').val(token);

                            var paymentData = self.getData();
                            placeOrder = placeOrderAction(paymentData, false, self.messageContainer);

                            $.when(placeOrder).fail(function () {
                                self.isPlaceOrderActionAllowed(true);
                            }).done(self.afterPlaceOrder.bind(self));
                            return true;
                        },
                        function (errors) {
                            self.handleErrors(errors);
                            self.isPlaceOrderActionAllowed(true);
                            return false;
                        }
                    );
                }
            } else {
                if (additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

                    $.when(placeOrder).fail(function () {
                        self.isPlaceOrderActionAllowed(true);
                    }).done(this.afterPlaceOrder.bind(this));
                    return true;
                }
            }
            return false;
        },
        afterPlaceOrder: function () {
            var method = this.getCode();
            var urlRedirect = window.checkoutConfig.payment[method].redirectUrl;
            window.location.replace(urlRedirect);
        }
    });
});