
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, modal, $t) {
    "use strict";

    $.widget('sumup.mpBlogAuthor', {
        options: {
            url: ''
        },
        isloaded: false,

        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function () {
            this.initCustomerGrid();
            this.selectCustomer();
        },

        /**
         * Init popup
         * Popup will automatic open
         */
        initPopup: function () {
            var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: $t('Select Customer'),
                    buttons: []
                },
                customerGridEl = $('#customer-grid');

            modal(options, customerGridEl);
            customerGridEl.modal('openModal');
        },

        /**
         * Init select customer
         */
        selectCustomer: function () {
            $('body').delegate('#customer-grid_table tbody tr', 'click', function () {
                var first_name = $(this).find('td:nth-child(3)').text().trim(),
                    last_name = $(this).find('td:nth-child(4)').text().trim();

                $("#author_customer_id").val($(this).find('input').val().trim());
                $("#author_customer").val(first_name+' '+last_name);
                $('#customer-grid').data('mageModal').closeModal();
            });
        },

        /**
         * Init customer grid
         */
        initCustomerGrid: function () {
            var self = this;

            $("#author_customer").click(function () {
                $.ajax({
                    method: 'POST',
                    url: self.options.url,
                    data: {form_key: window.FORM_KEY},
                    showLoader: true
                }).done(function (response) {
                    $('#customer-grid').html(response);
                    self.initPopup();
                });
            });
        }
    });

    return $.sumup.mpBlogAuthor;
});

