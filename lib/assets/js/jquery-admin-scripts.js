jQuery(document).ready(
        function ($) {

                var EPL_Admin = {

                        init: function () {
                                this.admin_listings_map();
                                EPL_Admin.manual_update();
                                this.mark_featured();
                                this.init_conditionals();
                                this.handle_conditional_fields();
                                this.init_settings_conditionals();
                                this.handle_settings_conditional_fields();
                        },

                        /**
                         *
                         * @since 3.5.0 reverse tied coordinates to map so changing the coordinates automatically updates map to point to updated
                         */
                        admin_listings_map: function () {
                                if ($('#epl_admin_map_canvas').length === 0) {
                                        return false;
                                }

                                var address = $('#epl_admin_map_canvas').data('address');
                                var address_coordinates = '';

                                if (!window.google) {
                                        return false;
                                }

                                if (address === '') {
                                        address = epl_admin_vars.default_map_address; // Default to Australia.
                                }

                                function renderMapWithCoordinates(coordinates) {
                                        var map = new google.maps.Map(document.getElementById('epl_admin_map_canvas'), {
                                                zoom: 14,
                                                center: coordinates,
                                                mapTypeId: google.maps.MapTypeId.ROADMAP,
                                        });

                                        var mapMarker = new google.maps.Marker({
                                                position: coordinates,
                                                draggable: true,
                                        });

                                        google.maps.event.addListener(mapMarker, 'dragend', function (evt) {
                                                $('#property_address_coordinates').val(evt.latLng.lat() + ', ' + evt.latLng.lng());
                                        });

                                        google.maps.event.addListener(mapMarker, 'dragstart', function (evt) {
                                                // Handle drag start if needed.
                                        });

                                        map.setCenter(mapMarker.position);
                                        mapMarker.setMap(map);
                                }

                                function geocodeAndRenderMap(address) {
                                        var geocoder = new google.maps.Geocoder();
                                        geocoder.geocode({ "address": address }, function (results) {
                                                address_coordinates = results[0].geometry.location;
                                                renderMapWithCoordinates(address_coordinates);
                                                $('#property_address_coordinates').val(address_coordinates.lat().toString() + ', ' + address_coordinates.lng().toString());
                                        });
                                }

                                // Initial map rendering based on the coordinates or default address.
                                var initialCoordinates = $('#property_address_coordinates').val();
                                if (initialCoordinates) {
                                        // Parse the coordinates from the input field.
                                        var coordinatesArray = initialCoordinates.split(', ');
                                        var latitude = parseFloat(coordinatesArray[0]);
                                        var longitude = parseFloat(coordinatesArray[1]);
                                        if (!isNaN(latitude) && !isNaN(longitude)) {
                                                renderMapWithCoordinates(new google.maps.LatLng(latitude, longitude));
                                        } else {
                                                geocodeAndRenderMap(address);
                                        }
                                } else {
                                        geocodeAndRenderMap(address);
                                }

                                $(document.body).on('click', '.epl-geocoder-button', function () {

                                        var $obj = $(this);
                                        if ($obj.closest('form').find('#property_address_sub_number').length) {
                                                listingUnit = $obj.closest('form').find('#property_address_sub_number').val();
                                        } else if ($obj.closest('form').find('#property_address_lot_number').length) {
                                                listingUnit = $obj.closest('form').find('#property_address_lot_number').val();
                                        } else {
                                                listingUnit = '';
                                        }

                                        var address = listingUnit + ' ';
                                        var address_el = [
                                                'property_address_street_number',
                                                'property_address_street',
                                                'property_address_suburb',
                                                'property_address_state',
                                                'property_address_postal_code',
                                                'property_address_country'
                                        ];

                                        $(address_el).each(
                                                function (i, v) {
                                                        if ($obj.closest('form').find('#' + v).length > 0) {
                                                                address += $obj.closest('form').find('#' + v).val() + ' ';
                                                        }
                                                }
                                        );

                                        address = $.trim(address);
                                        $('#property_address_coordinates').val(''); // Clear coordinates when address changes.

                                        if (address) {
                                                geocodeAndRenderMap(address);
                                        }
                                });

                                // Monitor changes to property_address_coordinates input field.
                                $('#property_address_coordinates').on('change', function () {
                                        var coordinates = $(this).val();
                                        if (coordinates) {
                                                var coordinatesArray = coordinates.split(', ');
                                                var latitude = parseFloat(coordinatesArray[0]);
                                                var longitude = parseFloat(coordinatesArray[1]);
                                                if (!isNaN(latitude) && !isNaN(longitude)) {
                                                        renderMapWithCoordinates(new google.maps.LatLng(latitude, longitude));
                                                }
                                        }
                                });
                        },

                        manual_update: function () {

                                jQuery('#epl_manual_update_lookup').on(
                                        'click',
                                        function (e) {
                                                e.preventDefault();
                                                var url = window.location.href.split('?')[0];
                                                var querystring = window.location.href.split('?')[1];

                                                if (querystring == null) {
                                                        var newUrl = url + '?' + 'epl_manual_update=1';
                                                } else {
                                                        var newUrl = url + '?' + querystring + '&epl_manual_update=1';
                                                }
                                                window.location.replace(newUrl);

                                        }
                                );
                        },

                        mark_featured: function () {

                                $('.column-property_featured span').on(
                                        'click',
                                        function (e) {

                                                var _this = $(this);
                                                var id = $(this).closest('tr').attr('id');
                                                id = id.split('-');
                                                id = id[1];
                                                $.ajax(
                                                        {
                                                                type: "POST",
                                                                url: ajaxurl,
                                                                dataType: "json",
                                                                data: {
                                                                        'action': 'epl_update_featured_listing',
                                                                        'id': id,
                                                                        '_epl_nonce': epl_admin_vars.ajax_nonce
                                                                },
                                                                success: function (response) {

                                                                        _this.toggleClass('dashicons-star-empty dashicons-star-filled');

                                                                        if (response.featured == 'no') {
                                                                                _this.closest('tr').find('.epl-listing-labels .featured').remove();
                                                                        } else {
                                                                                _this.closest('tr').find('.epl-listing-labels').append('<li class="featured">Featured</li>');
                                                                        }
                                                                }
                                                        }
                                                );
                                        }
                                );
                        },

                        init_conditionals: function () {

                                var fields = $('.epl-form-field-wrap input, .epl-form-field-wrap select, .epl-form-field-wrap textarea, .epl-form-field-wrap .epl-form-field-help');

                                fields.each(function (index) {

                                        var current_field = $(this);
                                        var current_container = current_field.closest('.epl-form-field-wrap');
                                        var should_show = true;
                                        var show = $(this).data('show');

                                        if (!show) {
                                                return; // continue
                                        }
                                        var relation = show.relation || 'AND';
                                        var conditions = show.fields || {};

                                        if ('AND' == relation || 'and' == relation) {
                                                $.each(conditions, function (i, condition) {
                                                        var el = condition[0];
                                                        var operator = condition[1];
                                                        var value = condition[2];

                                                        if ($('#' + el).length) {
                                                                if ('=' === operator) {
                                                                        should_show = should_show & (value == $('#' + el).val());
                                                                } else if ('!=' === operator) {
                                                                        should_show = should_show & (value != $('#' + el).val());
                                                                } else if ('>=' === operator) {
                                                                        should_show = should_show & (parseFloat($('#' + el).val()) >= value);
                                                                } else if ('<=' === operator) {
                                                                        should_show = should_show & (parseFloat($('#' + el).val()) <= value);
                                                                } else if ('>' === operator) {
                                                                        should_show = should_show & (parseFloat($('#' + el).val()) > value);
                                                                } else if ('<' === operator) {
                                                                        should_show = should_show & (parseFloat($('#' + el).val()) < value);
                                                                }
                                                        }
                                                });
                                        } else {
                                                should_show = false;
                                                $.each(conditions, function (i, condition) {
                                                        var el = condition[0];
                                                        var operator = condition[1];
                                                        var value = condition[2];

                                                        if ($('#' + el).length) {
                                                                if ('=' === operator) {
                                                                        should_show = should_show || (value == $('#' + el).val())
                                                                } else if ('!=' === operator) {
                                                                        should_show = should_show || (value != $('#' + el).val())
                                                                } else if ('>=' === operator) {
                                                                        should_show = should_show || (parseFloat($('#' + el).val()) >= value);
                                                                } else if ('<=' === operator) {
                                                                        should_show = should_show || (parseFloat($('#' + el).val()) <= value);
                                                                } else if ('>' === operator) {
                                                                        should_show = should_show || (parseFloat($('#' + el).val()) > value);
                                                                } else if ('<' === operator) {
                                                                        should_show = should_show || (parseFloat($('#' + el).val()) < value);
                                                                }
                                                        }
                                                });

                                        }

                                        if (should_show) {
                                                current_container.show();
                                        } else {
                                                current_container.hide();
                                        }
                                });
                        },

                        /**
                        * Support for conditional fields in settings.
                        *
                        * @since 3.5.0
                        */
                        init_settings_conditionals: function () {

                                var fields = $('.epl-field input, .epl-field select, .epl-field textarea, .epl-field .epl-form-field-help');

                                fields.each(function (index) {

                                        var current_field = $(this);
                                        var current_container = current_field.closest('.epl-field');
                                        var should_show = true;
                                        var show = $(this).data('show');

                                        if (!show) {
                                                return; // continue
                                        }
                                        var relation = show.relation || 'AND';
                                        var conditions = show.fields || {};

                                        if ('AND' == relation || 'and' == relation) {
                                                $.each(conditions, function (i, condition) {
                                                        var el = condition[0];
                                                        var operator = condition[1];
                                                        var value = condition[2];

                                                        if ($('#' + el).length) {
                                                                if ('=' === operator) {
                                                                        should_show = should_show & (value == $('#' + el).val());
                                                                } else if ('!=' === operator) {
                                                                        should_show = should_show & (value != $('#' + el).val());
                                                                } else if ('>=' === operator) {
                                                                        should_show = should_show & (parseFloat($('#' + el).val()) >= value);
                                                                } else if ('<=' === operator) {
                                                                        should_show = should_show & (parseFloat($('#' + el).val()) <= value);
                                                                } else if ('>' === operator) {
                                                                        should_show = should_show & (parseFloat($('#' + el).val()) > value);
                                                                } else if ('<' === operator) {
                                                                        should_show = should_show & (parseFloat($('#' + el).val()) < value);
                                                                }
                                                        }
                                                });
                                        } else {
                                                should_show = false;
                                                $.each(conditions, function (i, condition) {
                                                        var el = condition[0];
                                                        var operator = condition[1];
                                                        var value = condition[2];

                                                        if ($('#' + el).length) {
                                                                if ('=' === operator) {
                                                                        should_show = should_show || (value == $('#' + el).val())
                                                                } else if ('!=' === operator) {
                                                                        should_show = should_show || (value != $('#' + el).val())
                                                                } else if ('>=' === operator) {
                                                                        should_show = should_show || (parseFloat($('#' + el).val()) >= value);
                                                                } else if ('<=' === operator) {
                                                                        should_show = should_show || (parseFloat($('#' + el).val()) <= value);
                                                                } else if ('>' === operator) {
                                                                        should_show = should_show || (parseFloat($('#' + el).val()) > value);
                                                                } else if ('<' === operator) {
                                                                        should_show = should_show || (parseFloat($('#' + el).val()) < value);
                                                                }
                                                        }
                                                });

                                        }

                                        if (should_show) {
                                                current_container.show();
                                        } else {
                                                current_container.hide();
                                        }
                                });
                        },

                        handle_conditional_fields: function () {
                                var to_check = $('.epl-form-field-wrap input, .epl-form-field-wrap select, .epl-form-field-wrap textarea');
                                to_check.on('change', function (e) {
                                        EPL_Admin.init_conditionals();
                                });
                        },

                        /**
                        * Support for conditional fields in settings.
                        *
                        * @since 3.5.0
                        */
                        handle_settings_conditional_fields: function () {
                                var to_check = $('.epl-field input, .epl-field select, .epl-field textarea');
                                to_check.on('change', function (e) {
                                        EPL_Admin.init_settings_conditionals();
                                });

                                to_check.each(function () {
                                        $(this).trigger('change');
                                });
                        }
                }

                EPL_Admin.init();
                var formfield;

                function epl_update_query_string(key, value, url) {
                        if (!url) {
                                url = window.location.href;
                        }
                        var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
                                hash;

                        if (re.test(url)) {
                                if (typeof value !== 'undefined' && value !== null) {
                                        return url.replace(re, '$1' + key + "=" + value + '$2$3');
                                } else {
                                        hash = url.split('#');
                                        url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
                                        if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
                                                url += '#' + hash[1];
                                        }
                                        return url;
                                }
                        } else {
                                if (typeof value !== 'undefined' && value !== null) {
                                        var separator = url.indexOf('?') !== -1 ? '&' : '?';
                                        hash = url.split('#');
                                        url = hash[0] + separator + key + '=' + value;
                                        if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
                                                url += '#' + hash[1];
                                        }
                                        return url;
                                } else {
                                        return url;
                                }
                        }
                }

                if ($('input[name="epl_upload_button"]').length > 0) {
                        if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
                                $(document).on(
                                        'click',
                                        'input[name="epl_upload_button"]',
                                        function (e) {
                                                e.preventDefault();
                                                var button = $(this);
                                                var id = button.prev();
                                                wp.media.editor.send.attachment = function (props, attachment) {
                                                        console.log(attachment);
                                                        id.val(attachment.url);
                                                };
                                                wp.media.editor.open(button);
                                                return false;
                                        }
                                );
                        }
                }

                $('.dependency-true').each(
                        function () {
                                var $this = $(this);
                                var data_parent = $this.attr('data-parent');
                                if ($('select[name="' + data_parent + '"]').length) {
                                        if ($this.attr('data-type') == 'taxonomy') {
                                                var default_value = $this.attr('data-default');
                                                $('select[name="' + data_parent + '"]').change(
                                                        function () {
                                                                $.ajax(
                                                                        {
                                                                                type: "POST",
                                                                                url: ajaxurl,
                                                                                data: {
                                                                                        'parent_id': $(this).val(),
                                                                                        'type_name': $this.attr('data-type-name'),
                                                                                        'type': $this.attr('data-type'),
                                                                                        'default_value': default_value,
                                                                                        'action': 'epl_get_terms_drop_list',
                                                                                        '_epl_nonce': epl_admin_vars.ajax_nonce
                                                                                },
                                                                                success: function (response) {
                                                                                        $this.html(response);
                                                                                }
                                                                        }
                                                                );
                                                        }
                                                ).trigger('change');
                                        }
                                }
                        }
                );

                /* add datepicker for input type date */

                if ($(".epldatepicker").length) {

                        $('.epldatepicker').each(
                                function (e) {

                                        var tp = $(this).data('timepicker');
                                        var format = $(this).data('format');
                                        console.log(format);
                                        tp = tp == 1 ? true : false;
                                        $(this).epl_datetimepicker(
                                                {
                                                        'timepicker': tp,
                                                        format: format
                                                }
                                        );
                                }
                        );
                }

                if ($("#property_auction").length) {
                        $("#property_auction").epl_datetimepicker(
                                {
                                        format: "Y-m-d H:i",
                                        validateOnBlur: false,
                                        onSelectTime: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        },
                                        onSelectDate: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        },
                                        onGenerate: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        }
                                }
                        );
                }

                if ($("#property_sold_date").length) {
                        $("#property_sold_date").epl_datetimepicker(
                                {
                                        'timepicker': false,
                                        format: "Y-m-d",
                                        validateOnBlur: false,
                                        onSelectTime: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        },
                                        onSelectDate: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        },
                                        onGenerate: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        }
                                }
                        );
                }

                if ($("#property_list_date").length) {
                        $("#property_list_date").epl_datetimepicker(
                                {
                                        'timepicker': false,
                                        format: "Y-m-d",
                                        validateOnBlur: false,
                                        onSelectTime: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        },
                                        onSelectDate: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        },
                                        onGenerate: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        }
                                }
                        );
                }

                if ($("#property_com_lease_end_date").length) {
                        $("#property_com_lease_end_date").epl_datetimepicker(
                                {
                                        'timepicker': false,
                                        format: "Y-m-d",
                                        validateOnBlur: false,
                                        onSelectTime: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        },
                                        onSelectDate: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        },
                                        onGenerate: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        }
                                }
                        );
                }

                if ($("#property_date_available").length) {
                        $("#property_date_available").epl_datetimepicker(
                                {
                                        'timepicker': false,
                                        format: "Y-m-d",
                                        validateOnBlur: false,
                                        onSelectTime: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        },
                                        onSelectDate: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        },
                                        onGenerate: function (ct, $i) {
                                                var value = $i.val();
                                                value = value.replace(' ', 'T');
                                                $i.val(value);
                                        }
                                }
                        );
                }

                if ($("#property_inspection_times").length) {

                        $("#property_inspection_times").hide();
                        var eplAddedInspection = $("#property_inspection_times").val();
                        eplAddedInspection = eplAddedInspection.split('\n');

                        var epl_inspection_markup = epl_generate_inspection_markup();
                        epl_inspection_markup = '<tr class="form-field"><td>' + epl_inspection_markup + '</td></tr>';
                        $("#property_inspection_times").closest('.form-field').after(epl_inspection_markup);

                        if ($.trim(eplAddedInspection) != '') {
                                $.each(
                                        eplAddedInspection,
                                        function (key, value) {
                                                $('.epl-added-inspection').append('<span><span class="epl-inspection-text">' + value + '</span><span class="del-inspection-time">X</span></span>');
                                        }
                                );
                        }

                        jQuery('#epl-inspection-date').epl_datetimepicker({ 'timepicker': false, 'format': 'd-M-Y', 'closeOnDateSelect': true, 'allowBlank': false });
                        jQuery('#epl-inspection-start-hh').epl_datetimepicker({ 'datepicker': false, 'format': 'h', 'hours12': true });
                        jQuery('#epl-inspection-start-mm').epl_datetimepicker(
                                {
                                        'datepicker': false,
                                        'format': 'i',
                                        'allowTimes': ['00:00', '00:05', '00:10', '00:15', '00:20', '00:25', '00:30', '00:35', '00:40', '00:45', '00:50', '00:55']
                                }
                        );
                        jQuery('#epl-inspection-end-hh').epl_datetimepicker({ 'datepicker': false, 'format': 'h', 'hours12': true });
                        jQuery('#epl-inspection-end-mm').epl_datetimepicker(
                                {
                                        'datepicker': false,
                                        'format': 'i',
                                        'allowTimes': ['00:00', '00:05', '00:10', '00:15', '00:20', '00:25', '00:30', '00:35', '00:40', '00:45', '00:50', '00:55']
                                }
                        );

                }

                if (jQuery("form").length && "function" === typeof jQuery.fn.validationEngine) {
                        jQuery("form").validationEngine();
                }

                /* Handle Delition of inspection times */
                $(document).on(
                        'click',
                        '.del-inspection-time',
                        function () {
                                var currInspection = $(this).parent().find('.epl-inspection-text').text();
                                var eplAddedInspection = $("#property_inspection_times").val();
                                eplAddedInspection = eplAddedInspection.split('\n');
                                eplAddedInspection = jQuery.grep(
                                        eplAddedInspection,
                                        function (value) {
                                                return value != currInspection;
                                        }
                                );
                                eplAddedInspection = eplAddedInspection.join('\n');
                                $("#property_inspection_times").val(eplAddedInspection);
                                $(this).parent().fadeOut(
                                        600,
                                        function () {
                                                $(this).remove();
                                        }
                                )
                        }
                );

                /* Handle addition of inspection time */
                $(document).on(
                        'click',
                        '#epl-inspection-add',
                        function (e) {
                                e.preventDefault();
                                // Make inspection time as per format.
                                var added = $('#epl-inspection-date').val() + ' ' + $('#epl-inspection-start-hh').val() + ':' + $('#epl-inspection-start-mm').val() + $('#epl-inspection-start-ampm').val() + ' to ' + $('#epl-inspection-end-hh').val() + ':' + $('#epl-inspection-end-mm').val() + $('#epl-inspection-end-ampm').val();

                                var eplAddedInspection = $("#property_inspection_times").val();
                                eplAddedInspection = eplAddedInspection.split('\n');

                                if ($.inArray(added, eplAddedInspection) == -1) {
                                        eplAddedInspection.push(added);
                                        var newInspection = $('<span><span class="epl-inspection-text">' + added + '</span><span class="del-inspection-time">X</span></span>');
                                        eplAddedInspection = eplAddedInspection.join('\n');
                                        $("#property_inspection_times").val(eplAddedInspection);
                                        newInspection.hide().appendTo('.epl-added-inspection').fadeIn(600);

                                }
                        }
                );

                $('.epl-price-bar').hover(
                        function () { $(this).addClass('shine') },
                        function () { $(this).removeClass('shine') }
                )

                /* Extensions menus tabs */
                jQuery('.epl-fields-tab-menu ul li').click(
                        function () {
                                var fields_tab_id = jQuery(this).attr('data-tab');

                                /** Hack to not load page on click of sub tabs. **/
                                var revised_url = epl_update_query_string('sub_tab', fields_tab_id);
                                history.pushState('', '', revised_url);

                                jQuery('.epl-fields-tab-menu ul li').removeClass('epl-fields-menu-current');
                                jQuery('.epl-fields-single-menu').removeClass('epl-fields-field-current');

                                // Show / hide corresponding help text.
                                jQuery('.epl-field-intro').removeClass('active epl-show-help');
                                jQuery('[data-help="' + fields_tab_id + '"]').addClass('active epl-show-help');

                                jQuery(this).addClass('epl-fields-menu-current');
                                console.log("#tab-menu-" + fields_tab_id);
                                jQuery("#" + fields_tab_id).addClass('epl-fields-field-current');
                        }
                );
                // jQuery('.epl-fields-tab-menu ul li:first').trigger('click');.

                /* upgrade db button */
                if ($(".epl-upgrade-btn").length) {

                        function process_upgrade_db() {

                                var upgrade_ver = $('.epl-upgrade-btn').data('upgrade');
                                var btn = $('.epl-upgrade-btn');
                                var postData = {
                                        action: 'epl_upgrade_db',
                                        upgrade_to: upgrade_ver,
                                        '_epl_nonce': epl_admin_vars.ajax_nonce
                                };
                                $('.epl-ajax-notice').show();

                                $.ajax(
                                        {
                                                method: "POST",
                                                url: ajaxurl,
                                                data: postData,
                                                dataType: 'json'
                                        }
                                )
                                        .done(
                                                function (response) {

                                                        if (response.status == 'success') {

                                                                $('.epl-ajax-notice')
                                                                        .removeClass(' ajax-fail ajax-success')
                                                                        .addClass(' ajax-success').html(response.msg);

                                                                if (response.buffer == 'processing') {
                                                                        try {
                                                                                process_upgrade_db();
                                                                        } catch (err) {
                                                                                $('.epl-ajax-notice')
                                                                                        .removeClass(' ajax-fail ajax-success')
                                                                                        .addClass(' ajax-fail')
                                                                                        .html('Please reload page & process again.');
                                                                        }
                                                                }
                                                        } else {
                                                                $('.epl-ajax-notice')
                                                                        .removeClass(' ajax-fail ajax-success')
                                                                        .addClass(' ajax-fail').html(response.msg);
                                                        }

                                                }
                                        );

                        }

                        $('.epl-upgrade-btn').on(
                                'click',
                                function (e) {
                                        e.preventDefault();
                                        process_upgrade_db();
                                }
                        );

                }

                /**
                * Contact management screen JS
                */
                var EPL_Contact = {

                        vars: {
                                contact_card_wrap_editable: $('.epl-contact-card-wrapper .editable'),
                                contact_card_wrap_edit_item: $('.epl-contact-card-wrapper .edit-item'),
                                user_id: $('input[name="contactinfo[user_id]"]'),
                                state_input: $(':input[name="contactinfo[state]"]'),
                                note: $('#epl_contact_activity_content'),
                        },
                        init: function () {
                                this.edit_contact();
                                this.user_search();
                                this.remove_user();
                                this.cancel_edit();
                                this.change_country();
                                this.add_note();
                                this.add_note_note_tab();
                                this.add_listing();
                                this.delete_checked();
                        },
                        edit_contact: function () {
                                $(document.body).on(
                                        'click',
                                        '#edit-contact',
                                        function (e) {
                                                e.preventDefault();

                                                EPL_Contact.vars.contact_card_wrap_editable.hide();
                                                EPL_Contact.vars.contact_card_wrap_edit_item.fadeIn().css('display', 'block');
                                        }
                                );
                        },
                        user_search: function () {
                                // Upon selecting a user from the dropdown, we need to update the User ID.
                                $(document.body).on(
                                        'click.eplSelectUser',
                                        '.epl_user_search_results a',
                                        function (e) {
                                                e.preventDefault();
                                                var user_id = $(this).data('userid');
                                                EPL_Contact.vars.user_id.val(user_id);
                                        }
                                );
                        },
                        remove_user: function () {
                                $(document.body).on(
                                        'click',
                                        '#disconnect-contact',
                                        function (e) {
                                                e.preventDefault();
                                                var contact_id = $('input[name="contactinfo[id]"]').val();

                                                var postData = {
                                                        epl_action: 'disconnect-userid',
                                                        contact_id: contact_id,
                                                        _wpnonce: $('#edit-contact-info #_wpnonce').val()
                                                };

                                                $.post(
                                                        ajaxurl,
                                                        postData,
                                                        function (response) {

                                                                window.location.href = window.location.href;

                                                        },
                                                        'json'
                                                );

                                        }
                                );
                        },
                        cancel_edit: function () {
                                $(document.body).on(
                                        'click',
                                        '#epl-edit-contact-cancel',
                                        function (e) {
                                                e.preventDefault();
                                                EPL_Contact.vars.contact_card_wrap_edit_item.hide();
                                                EPL_Contact.vars.contact_card_wrap_editable.show();

                                                $('.epl_user_search_results').html('');
                                        }
                                );
                        },
                        change_country: function () {
                                $('select[name="contactinfo[country]"]').change(
                                        function () {
                                                var $this = $(this);
                                                data = {
                                                        action: 'epl_get_shop_states',
                                                        country: $this.val(),
                                                        field_name: 'contactinfo[state]',
                                                        '_epl_nonce': epl_admin_vars.ajax_nonce
                                                };
                                                $.post(
                                                        ajaxurl,
                                                        data,
                                                        function (response) {
                                                                if ('nostates' == response) {
                                                                        EPL_Contact.vars.state_input.replaceWith('<input type="text" name="' + data.field_name + '" value="" class="epl-edit-toggles medium-text"/>');
                                                                } else {
                                                                        EPL_Contact.vars.state_input.replaceWith(response);
                                                                }
                                                        }
                                                );

                                                return false;
                                        }
                                );
                        },
                        add_note_note_tab: function () {
                                $(document.body).on(
                                        'click',
                                        '#add-contact-note',
                                        function (e) {
                                                e.preventDefault();
                                                var postData = {
                                                        epl_action: 'add-contact-note-note-tab',
                                                        contact_id: $('#epl-contact-id').val(),
                                                        listing_id: $('#contact-note-listing').val(),
                                                        activity_type: $('#contact-activity-type').val(),
                                                        contact_note: $('#contact-note').val(),
                                                        add_contact_note_nonce: $('#add_contact_note_nonce').val()
                                                };
                                                console.log(postData);
                                                if (postData.contact_note) {

                                                        $.ajax(
                                                                {
                                                                        type: "POST",
                                                                        data: postData,
                                                                        url: ajaxurl,
                                                                        success: function (response) {
                                                                                $('#epl-contact-notes').prepend(response);
                                                                                $('.epl-no-contact-notes').hide();
                                                                                $('#contact-note').val('');
                                                                        }
                                                                }
                                                        ).fail(
                                                                function (data) {
                                                                        if (window.console && window.console.log) {
                                                                                console.log(data);
                                                                        }
                                                                }
                                                        );

                                                } else {
                                                        var border_color = EPL_Contact.vars.note.css('border-color');
                                                        EPL_Contact.vars.note.css('border-color', 'red');
                                                        setTimeout(
                                                                function () {
                                                                        EPL_Contact.vars.note.css('border-color', border_color);
                                                                },
                                                                500
                                                        );
                                                }
                                        }
                                );
                        },
                        add_note: function () {
                                $(document.body).on(
                                        'click',
                                        '#epl_contact_activity_submit',
                                        function (e) {
                                                e.preventDefault();
                                                var postData = {
                                                        epl_action: 'add-contact-note',
                                                        contact_id: $('#epl_contact_id').val(),
                                                        listing_id: $('#epl_contact_activity_listing').val(),
                                                        note_type: $('#epl_contact_activity_type').val(),
                                                        contact_note: EPL_Contact.vars.note.val(),
                                                        add_contact_note_nonce: $('#add_contact_note_nonce').val()
                                                };

                                                if (postData.contact_note) {

                                                        $.ajax(
                                                                {
                                                                        type: "POST",
                                                                        data: postData,
                                                                        url: ajaxurl,
                                                                        success: function (response) {
                                                                                EPL_Contact.vars.note.val('');
                                                                                $('.epl-contact-activities tbody').prepend(response);
                                                                        }
                                                                }
                                                        ).fail(
                                                                function (data) {
                                                                        if (window.console && window.console.log) {
                                                                                console.log(data);
                                                                        }
                                                                }
                                                        );

                                                } else {
                                                        var border_color = EPL_Contact.vars.note.css('border-color');
                                                        EPL_Contact.vars.note.css('border-color', 'red');
                                                        setTimeout(
                                                                function () {
                                                                        EPL_Contact.vars.note.css('border-color', border_color);
                                                                },
                                                                500
                                                        );
                                                }
                                        }
                                );
                        },
                        add_listing: function () {
                                $(document.body).on(
                                        'click',
                                        '#contact_listing_submit',
                                        function (e) {
                                                e.preventDefault();
                                                var postData = data = $('#epl_contact_add_listing_form').serialize();
                                                postData += '&epl_action=add-contact-listing&contact_id=' + $('#epl_contact_id').val() + '&_epl_nonce=' + epl_admin_vars.ajax_nonce;

                                                if (postData) {
                                                        $.ajax(
                                                                {
                                                                        type: "POST",
                                                                        data: postData,
                                                                        url: ajaxurl,
                                                                        success: function (response) {
                                                                                EPL_Contact.vars.note.val('');
                                                                                $('.epl-contact-listings tbody').prepend(response);
                                                                        }
                                                                }
                                                        ).fail(
                                                                function (data) {
                                                                        if (window.console && window.console.log) {
                                                                                console.log(data);
                                                                        }
                                                                }
                                                        );

                                                } else {
                                                        var border_color = EPL_Contact.vars.note.css('border-color');
                                                        EPL_Contact.vars.note.css('border-color', 'red');
                                                        setTimeout(
                                                                function () {
                                                                        EPL_Contact.vars.note.css('border-color', border_color);
                                                                },
                                                                500
                                                        );
                                                }
                                        }
                                );
                        },
                        delete_checked: function () {
                                $('#epl-contact-delete-confirm').change(
                                        function () {
                                                var records_input = $('#epl-contact-delete-records');
                                                var submit_button = $('#epl-delete-contact');

                                                if ($(this).prop('checked')) {
                                                        records_input.attr('disabled', false);
                                                        submit_button.attr('disabled', false);
                                                } else {
                                                        records_input.attr('disabled', true);
                                                        records_input.prop('checked', false);
                                                        submit_button.attr('disabled', true);
                                                }
                                        }
                                );
                        }

                };
                EPL_Contact.init();

                $('.contact-action-category').on(
                        'click',
                        function (e) {
                                e.preventDefault();
                                e.stopPropagation();
                                jQuery('.epl-contact_category_suggestions').slideToggle(100);
                        }
                );

                $('.contact-action-tag').on(
                        'click',
                        function (e) {
                                e.preventDefault();
                                e.stopPropagation();
                                jQuery('.epl-contact-tags-find').slideToggle(100);
                                $('#contact-tag-hint').focus();
                        }
                );

                $(document).on(
                        'click',
                        function (e) {

                                if ($('.epl-contact_category_suggestions').is(':visible')) {
                                        jQuery('.epl-contact_category_suggestions').slideUp(100);
                                }

                                if ($('.epl-contact-tags-find').is(':visible') && !$('.epl-contact-tags-find > input').is(':focus')) {
                                        jQuery('.epl-contact-tags-find').slideUp(100);
                                }

                        }
                );

                $('.epl-contact_category_suggestions > li >  a').on(
                        'click',
                        function (e) {
                                e.preventDefault();
                                var label = $(this).data('label')
                                $.ajax(
                                        {
                                                type: "POST",
                                                data: {

                                                        action: "epl_contact_category_update",
                                                        type: $(this).data('key'),
                                                        contact_id: $('#epl_contact_id').val(),
                                                        '_epl_nonce': epl_admin_vars.ajax_nonce
                                                },
                                                dataType: "json",
                                                url: ajaxurl,
                                                success: function (success) {
                                                        if (success) {
                                                                $('.epl-contact-entry-header span').text(label);
                                                        }

                                                }
                                        }
                                );
                        }
                );

                $('.epl-contact_tags_suggestions > li').on(
                        'click',
                        function (e) {
                                e.preventDefault();
                                var data = {
                                        action: "contact_tags_update",
                                        contact_id: $('#epl_contact_id').val(),
                                        term_id: $(this).data('id'),
                                        bg: $(this).data('bg'),
                                        label: $(this).text(),
                                        '_epl_nonce': epl_admin_vars.ajax_nonce
                                }
                                if ($('#contact-tag-' + data.term_id).length >= 1) {
                                        return false;
                                }

                                $.ajax(
                                        {
                                                type: "POST",
                                                data: data,
                                                url: ajaxurl,
                                                success: function (success) {
                                                        $('#contact-tag-hint').val('');
                                                        $('.epl-contact_tags_suggestions > li').hide();
                                                        if (success) {
                                                                $('#contact-tag-hint').val('');
                                                                var tpl = '<li style="background:' + data.bg + '" data-id="' + data.term_id + '" id="contact-tag-' + data.term_id + '">' + data.label + '<span class="dashicons dashicons-no epl-contact-tag-del"></span></li>';
                                                                $('.epl-contact-assigned-tags').append(tpl);
                                                        }

                                                }
                                        }
                                );
                        }
                );

                $('.epl-contact-assigned-tags').on(
                        'click',
                        '.epl-contact-tag-del',
                        function (e) {
                                e.preventDefault();
                                var _this = $(this);
                                var data = {
                                        action: "epl_contact_tag_remove",
                                        contact_id: $('#epl_contact_id').val(),
                                        term_id: $(this).parent().data('id'),
                                        label: $(this).parent().text(),
                                        '_epl_nonce': epl_admin_vars.ajax_nonce
                                }
                                $.ajax(
                                        {
                                                type: "POST",
                                                data: data,
                                                url: ajaxurl,
                                                success: function (success) {
                                                        if (success) {
                                                                _this.parent().fadeIn(200).remove();
                                                        }

                                                }
                                        }
                                );
                        }
                );

                $('#contact-tag-hint').keyup(
                        function (e) {
                                var valThis = $(this).val();

                                if (valThis == '') {
                                        return false;
                                }

                                $('.epl-contact_tags_suggestions>li').each(
                                        function () {
                                                var text = $(this).text().toLowerCase();
                                                (text.indexOf(valThis) == 0) ? $(this).show() : $(this).hide();
                                        }
                                );

                                if (e.keyCode == 13) {
                                        var data = {
                                                action: "contact_tags_update",
                                                contact_id: $('#epl_contact_id').val(),
                                                term_id: valThis,
                                                bg: '#39b54a',
                                                '_epl_nonce': epl_admin_vars.ajax_nonce,
                                                label: valThis.charAt(0).toUpperCase() + valThis.slice(1) // capitalize first letter.
                                        }
                                        if ($('#contact-tag-' + epl.helpers.string_to_name(data.term_id).length) >= 1) {
                                                return false;
                                        }

                                        $.ajax(
                                                {
                                                        type: "POST",
                                                        data: data,
                                                        url: ajaxurl,
                                                        success: function (success) {
                                                                $('#contact-tag-hint').val('');
                                                                $('.epl-contact_tags_suggestions > li').hide();
                                                                if (success) {
                                                                        $('#contact-tag-hint').val('');
                                                                        var tpl = '<li style="background:' + data.bg + '" data-id="' + success + '" id="contact-tag-' + success + '">' + data.term_id + '<span class="dashicons dashicons-no epl-contact-tag-del"></span></li>';
                                                                        $('.epl-contact-assigned-tags').append(tpl);
                                                                        jQuery('.epl-contact-tags-find').slideUp(100);
                                                                }

                                                        }
                                                }
                                        );
                                }
                        }
                );

                $('.epl-contact-add-activity').on(
                        'click',
                        function () {
                                var val = $('.epl-contact-add-activity').text() == 'Add New' ? 'Close' : 'Add New';
                                $('.epl-contact-add-activity').text(val);
                                $('#epl-contact-add-activity-wrap').slideToggle(100);
                        }
                );

                $('.epl-contact-add-listing').on(
                        'click',
                        function () {
                                $('#epl-contact-add-listing-wrap').slideToggle(100);
                        }
                );

                $('.epl-contact-addable:last').after('<span class="epl-contact-add-new-num">add another</span>');
                $('.epl-contact-addable-email:last').after('<span class="epl-contact-add-new-email">add another</span>');

                $(document).on(
                        'click',
                        '.epl-contact-add-new-num',
                        function () {
                                var name = epl.helpers.get_unique_name('phone', 'contact_phones');
                                var tpl = '\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="epl-contact-new-num">\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="number" maxlength="60" class="epl-contact-sub" name="' + name + '">\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <select id="epl-contact-sub-select" type="select" class="epl-contact-note-select" > \
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <option value="phone">Phone</option>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <option value="mobile">Mobile</option>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <option value="office">Office</option>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <option value="home">Home</option>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </select>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ';
                                $(this).before(tpl);
                        }
                );

                $(document).on(
                        'click',
                        '.epl-contact-add-new-email',
                        function () {
                                var name = epl.helpers.get_unique_name('email', 'contact_emails');
                                var tpl = '\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="epl-contact-new-email">\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <input type="email" maxlength="60" class="epl-contact-sub" name="' + name + '">\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <select id="epl-contact-sub-mail" type="select" class="epl-contact-note-select" > \
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <option value="email">Email</option>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <option value="work">Work</option>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <option value="office">Office</option>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <option value="home">Home</option>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </select>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ';
                                $(this).before(tpl);
                        }
                );

                if (jQuery('.epl-contact-view-more').length == 1 && jQuery('.epl-contact-main-wrapper.left')[0].scrollHeight >= 195) {
                        jQuery('.epl-contact-view-more').css('display', 'block');
                }
                jQuery('.epl-contact-view-more').on(
                        'click',
                        function () {
                                var hight = jQuery(this).closest('.epl-contact-main-wrapper.left').height();
                                if (hight <= 190) {
                                        jQuery(this).closest('.epl-contact-main-wrapper.left').height('auto');
                                        jQuery(this).css('position', 'relative').html('<span class="dashicons dashicons-arrow-right-alt epl-contact-icons"></span>View Less');
                                } else {
                                        jQuery(this).closest('.epl-contact-main-wrapper.left').height('190px');
                                        jQuery(this).css('position', 'absolute').html('<span class="dashicons dashicons-arrow-right-alt epl-contact-icons"></span>View More');

                                }
                                console.log(hight);
                        }
                );

                $(document).on(
                        'click',
                        '#epl-contact-sub-select',
                        function () {
                                var name = epl.helpers.get_unique_name($(this).val(), 'contact_phones');
                                $(this).prev().attr('name', name);
                        }
                );
                $(document).on(
                        'click',
                        '#epl-contact-sub-mail',
                        function () {
                                var name = epl.helpers.get_unique_name($(this).val(), 'contact_emails');
                                $(this).prev().attr('name', name);
                        }
                );

                $('#epl-item-tables-wrapper').on(
                        'click',
                        'th',
                        function (e) {

                                e.preventDefault();
                                var _this = $(this);
                                $('#epl-contact-table-orderby').val($(this).data('sort'));
                                console.log($('#epl-contact-table-orderby').val());

                                if (_this.hasClass('epl-sorted-desc')) {
                                        _this.addClass('epl-sorted-asc');
                                        $('#epl-contact-table-order').val('ASC');
                                } else {
                                        $('#epl-contact-table-order').val('DESC');
                                        _this.addClass('epl-sorted-desc');
                                }
                                var data = {
                                        action: "epl_contact_get_activity_table",
                                        contact: $('#epl_contact_id').val(),
                                        number: 10, // change later.
                                        paged: 1,
                                        orderby: $('#epl-contact-table-orderby').val(),
                                        order: $('#epl-contact-table-order').val(),
                                        '_epl_nonce': epl_admin_vars.ajax_nonce

                                }
                                $.ajax(
                                        {
                                                type: "POST",
                                                data: data,
                                                url: ajaxurl,
                                                success: function (success) {
                                                        if (success) {
                                                                $('#epl-contact-activity-table-wrapper').html(success);
                                                        }

                                                }
                                        }
                                );
                        }
                );

                $(document).on(
                        'click',
                        '.epl-contact-load-activities',
                        function (e) {
                                var next = $(this).data('page');
                                var data = {
                                        action: "epl_contact_get_activity_table",
                                        contact: $('#epl_contact_id').val(),
                                        number: 10, // change later.
                                        paged: next,
                                        orderby: $('#epl-contact-table-orderby').val(),
                                        order: $('#epl-contact-table-order').val(),
                                        '_epl_nonce': epl_admin_vars.ajax_nonce

                                }
                                $.ajax(
                                        {
                                                type: "POST",
                                                data: data,
                                                url: ajaxurl,
                                                success: function (success) {
                                                        if (success != false) {
                                                                var newenteries = $(success).find('tbody').html();
                                                                var pager = $(success).find('.epl-contact-load-activities').data('page');
                                                                $('#epl-contact-activity-table-wrapper').find('tbody').append(newenteries);
                                                                $('.epl-contact-load-activities').data('page', pager);
                                                        }

                                                }
                                        }
                                );
                        }
                );

                /**
                * Reports / Exports screen JS
                */
                var EPL_Reports = {

                        init: function () {
                                this.date_options();
                        },

                        date_options: function () {

                                // Show hide extended date options.
                                $('#epl-graphs-date-options').change(
                                        function () {
                                                var $this = $(this),
                                                        date_range_options = $('#epl-date-range-options');

                                                if ('other' === $this.val()) {
                                                        date_range_options.show();
                                                } else {
                                                        date_range_options.hide();
                                                }
                                        }
                                );

                        },

                };
                EPL_Reports.init();

                if ($('#property_owner').length == 1) {

                        var EplContactTimeout = null;

                        $('#property_owner').keyup(
                                function () {

                                        if ($('#property_owner').val() == '') {
                                                $('.epl-contact-listing-suggestion').fadeOut();
                                                return false;
                                        }

                                        if (EplContactTimeout != null) {
                                                clearTimeout(EplContactTimeout);
                                        }
                                        var user_search = $(this).val();

                                        data = {
                                                action: 'epl_search_contact',
                                                user_name: user_search,
                                                '_epl_nonce': epl_admin_vars.ajax_nonce
                                        };

                                        EplContactTimeout = setTimeout(
                                                function () {
                                                        EplContactTimeout = null;

                                                        $.ajax(
                                                                {
                                                                        type: "POST",
                                                                        data: data,
                                                                        url: ajaxurl,
                                                                        success: function (response) {
                                                                                if ($('.epl-contact-listing-suggestion').length == 1) {
                                                                                        $('.epl-contact-listing-suggestion').replaceWith(response);
                                                                                } else {
                                                                                        $('#property_owner').after(response);
                                                                                }
                                                                                $('.epl-contact-listing-suggestion').fadeIn();
                                                                        }
                                                                }
                                                        );
                                                },
                                                300
                                        );

                                }
                        );

                        $(document.body).on(
                                'click',
                                '.epl-contact-listing-suggestion li',
                                function (e) {
                                        $('#property_owner').val($(this).data('id'));
                                        $('.epl-contact-listing-suggestion').fadeOut();
                                }
                        );
                }

                if ($('#epl_contact_wp_user').length == 1) {

                        var EplUserTimeout = null;

                        $('#epl_contact_wp_user').keyup(
                                function (e) {
                                        var currTarget = e.currentTarget;
                                        document.body.style.cursor = 'progress';
                                        if ($(currTarget).val() == '') {
                                                $('.epl-contact-user-suggestion').fadeOut();
                                                return false;
                                        }

                                        if (EplUserTimeout != null) {
                                                clearTimeout(EplUserTimeout);
                                        }
                                        var user_search = $(this).val();

                                        data = {
                                                action: 'epl_search_user',
                                                user_name: user_search,
                                                '_epl_nonce': epl_admin_vars.ajax_nonce
                                        };

                                        EplUserTimeout = setTimeout(
                                                function () {
                                                        EplUserTimeout = null;

                                                        $.ajax(
                                                                {
                                                                        type: "POST",
                                                                        data: data,
                                                                        url: ajaxurl,
                                                                        success: function (response) {
                                                                                document.body.style.cursor = 'auto';
                                                                                if ($('.epl-contact-user-suggestion').length == 1) {
                                                                                        $('.epl-contact-user-suggestion').replaceWith(response);
                                                                                } else {
                                                                                        $(currTarget).after(response);
                                                                                }
                                                                                $('.epl-contact-user-suggestion').fadeIn();
                                                                        }
                                                                }
                                                        );
                                                },
                                                300
                                        );

                                }
                        );

                        $(document.body).on(
                                'click',
                                '.epl-contact-user-suggestion li',
                                function (e) {
                                        $('#epl_contact_wp_user').val($(this).text());
                                        $('#epl_contact_wp_user_id').val($(this).data('id'));
                                        $('.epl-contact-user-suggestion').fadeOut();
                                }
                        );
                }

                if ($('.epl-widget-author-username').length > 0) {

                        var EplUserTimeout = null;

                        $('.epl-widget-author-username').keyup(
                                function (e) {

                                        var currTarget = e.currentTarget;
                                        document.body.style.cursor = 'progress';
                                        if ($(currTarget).val() == '') {
                                                $('.epl-contact-user-suggestion').fadeOut();
                                                return false;
                                        }

                                        if (EplUserTimeout != null) {
                                                clearTimeout(EplUserTimeout);
                                        }
                                        var user_search = $(this).val();
                                        user_search = user_search.split(',');
                                        user_search = user_search[user_search.length - 1];

                                        data = {
                                                action: 'epl_search_user',
                                                user_name: user_search,
                                                'exclude_roles': 'subscriber',
                                                '_epl_nonce': epl_admin_vars.ajax_nonce
                                        };

                                        EplUserTimeout = setTimeout(
                                                function () {
                                                        EplUserTimeout = null;

                                                        $.ajax(
                                                                {
                                                                        type: "POST",
                                                                        data: data,
                                                                        url: ajaxurl,
                                                                        success: function (response) {
                                                                                document.body.style.cursor = 'auto';
                                                                                if ($('.epl-contact-user-suggestion').length == 1) {
                                                                                        $('.epl-contact-user-suggestion').replaceWith(response);
                                                                                } else {
                                                                                        $(currTarget).after(response);
                                                                                }
                                                                                $('.epl-contact-user-suggestion').fadeIn();
                                                                        }
                                                                }
                                                        );
                                                },
                                                300
                                        );

                                }
                        );

                        $(document.body).on(
                                'click',
                                '.epl-contact-user-suggestion li',
                                function (e) {
                                        var input_field = $(this).closest('.epl-contact-user-suggestion').prev();
                                        var curr_val = input_field.val();
                                        curr_val_arr = curr_val.split(',');
                                        curr_val_arr.splice(-1, 1);
                                        curr_val = curr_val_arr.join(',');

                                        if (curr_val == '') {
                                                curr_val = $(this).data('uname') + ',';
                                        } else {
                                                curr_val = curr_val + ',' + $(this).data('uname') + ',';
                                        }

                                        input_field.val(curr_val);
                                        $('.epl-contact-user-suggestion').fadeOut().remove();
                                }
                        );
                }

                if ($('#property_second_agent').length == 1 || $('#property_agent').length == 1) {

                        var EplUserTimeout = null;

                        $('#property_fourth_agent,#property_third_agent,#property_second_agent,#property_agent').keyup(
                                function (e) {
                                        var currTarget = e.currentTarget;
                                        document.body.style.cursor = 'progress';
                                        if ($(currTarget).val() == '') {
                                                $('.epl-contact-user-suggestion').fadeOut();
                                                return false;
                                        }

                                        if (EplUserTimeout != null) {
                                                clearTimeout(EplUserTimeout);
                                        }
                                        var user_search = $(this).val();

                                        data = {
                                                action: 'epl_search_user',
                                                user_name: user_search,
                                                '_epl_nonce': epl_admin_vars.ajax_nonce
                                        };

                                        EplUserTimeout = setTimeout(
                                                function () {
                                                        EplUserTimeout = null;

                                                        $.ajax(
                                                                {
                                                                        type: "POST",
                                                                        data: data,
                                                                        url: ajaxurl,
                                                                        success: function (response) {
                                                                                document.body.style.cursor = 'auto';
                                                                                if ($('.epl-contact-user-suggestion').length == 1) {
                                                                                        $('.epl-contact-user-suggestion').replaceWith(response);
                                                                                } else {
                                                                                        $(currTarget).after(response);
                                                                                }
                                                                                $('.epl-contact-user-suggestion').fadeIn();
                                                                        }
                                                                }
                                                        );
                                                },
                                                300
                                        );

                                }
                        );

                        $(document.body).on(
                                'click',
                                '.epl-contact-user-suggestion li',
                                function (e) {
                                        $(this).closest('.epl-contact-user-suggestion').prev().val($(this).data('uname'));
                                        $('.epl-contact-user-suggestion').fadeOut().remove();
                                }
                        );
                }

                if ($('#epl_contact_listing_search').length == 1) {

                        var EplContactListingTimeout = null;

                        $('#epl_contact_listing_search').keyup(
                                function () {

                                        if ($('#epl_contact_listing_search').val() == '') {
                                                $('.epl-contact-owned-listing-suggestion').fadeOut();
                                                return false;
                                        }

                                        if (EplContactListingTimeout != null) {
                                                clearTimeout(EplContactListingTimeout);
                                        }
                                        var search = $(this).val();

                                        data = {
                                                action: 'epl_search_contact_listing',
                                                s: search,
                                                contact: $('#epl_contact_id').val(),
                                                '_epl_nonce': epl_admin_vars.ajax_nonce
                                        };

                                        EplContactListingTimeout = setTimeout(
                                                function () {
                                                        EplContactListingTimeout = null;

                                                        $.ajax(
                                                                {
                                                                        type: "POST",
                                                                        data: data,
                                                                        url: ajaxurl,
                                                                        success: function (response) {
                                                                                if ($('.epl-contact-owned-listing-suggestion').length == 1) {
                                                                                        $('.epl-contact-owned-listing-suggestion').replaceWith(response);
                                                                                } else {
                                                                                        $('#epl_contact_listing_search').after(response);
                                                                                }
                                                                                $('.epl-contact-owned-listing-suggestion').fadeIn();
                                                                        }
                                                                }
                                                        );
                                                },
                                                300
                                        );

                                }
                        );

                        $(document.body).on(
                                'click',
                                '.epl-contact-owned-listing-suggestion li',
                                function (e) {
                                        var id = $(this).data('id');

                                        $.ajax(
                                                {
                                                        type: "POST",
                                                        data: {
                                                                epl_action: 'add-existing-contact-listing',
                                                                contact_id: $('#epl_contact_id').val(),
                                                                id: id,
                                                                '_epl_nonce': epl_admin_vars.ajax_nonce
                                                        },
                                                        url: ajaxurl,
                                                        success: function (response) {
                                                                $('.epl-contact-listings tbody').prepend(response);
                                                                $('.epl-contact-owned-listing-suggestion').fadeOut();
                                                                $('#epl_contact_listing_search').val('');
                                                        }
                                                }
                                        );
                                }
                        );

                }

                $(document.body).on(
                        'click.eplSelectUser',
                        '.epl_user_search_results span a',
                        function (e) {
                                e.preventDefault();
                                var login = $(this).data('login');
                                $('.epl-ajax-user-search').val(login);
                                $('.epl_user_search_results').addClass('hidden');
                                $('.epl_user_search_results span').html('');
                        }
                );

                $(document.body).on(
                        'click.eplCancelUserSearch',
                        '.epl_user_search_results a.epl-ajax-user-cancel',
                        function (e) {
                                e.preventDefault();
                                $('.epl-ajax-user-search').val('');
                                $('.epl_user_search_results').addClass('hidden');
                                $('.epl_user_search_results span').html('');
                        }
                );

                $('#epl-contact-add-listing-wrap').on(
                        'blur',
                        'input',
                        function (e) {
                                var addr = '';
                                addr += $('#property_address_lot_number').val() != '' ? $('#property_address_lot_number').val() + '/' : '';
                                addr += $('#property_address_sub_number').val() != '' ? $('#property_address_sub_number').val() + ' ' : '';
                                addr += $('#property_address_street_number').val() != '' ? $('#property_address_street_number').val() + ' ' : '';
                                addr += $('#property_address_street').val() != '' ? $('#property_address_street').val() + ' ' : '';
                                addr += $('#property_address_suburb').val() != '' ? $('#property_address_suburb').val() + ' ' : '';
                                addr += $('#property_address_state').val() != '' ? $('#property_address_state').val() + ' ' : '';
                                addr += $('#property_address_postal_code').val() != '' ? $('#property_address_postal_code').val() + ' ' : '';
                                addr += $('#property_address_country').val() != '' ? $('#property_address_country').val() + ' ' : '';
                                $('#post_title').val(addr);
                        }
                );

                $(document).on(
                        'change',
                        '.epl_contact_type_filter',
                        function (e) {

                                var catFilter = $(this).val();
                                if (catFilter != '') {
                                        document.location.href = epl_update_query_string('cat_filter', catFilter);
                                }
                        }
                );

                $('.epl-contact-list-table-tags').on(
                        'click',
                        '.epl-contact-assigned-tags li',
                        function (e) {

                                var tag_filter = $(this).data('id');
                                if (tag_filter != '') {
                                        document.location.href = epl_update_query_string('tag_filter', tag_filter);
                                }
                        }
                );

                $('.epl-contact-all-tags-container > span .epl-tag-btn').on(
                        'mouseleave',
                        function (e) {
                                if ($(this).parent().hasClass('epl-lock')) {
                                        return false;
                                }
                                $(this).css(
                                        {
                                                'background': '#fff',
                                                'color': $(this).data('accent')
                                        }
                                );
                        }
                );

                $('.epl-contact-all-tags-container > span .epl-tag-btn').on(
                        'mouseenter',
                        function (e) {
                                if ($(this).parent().hasClass('epl-lock')) {
                                        return false;
                                }
                                $(this).css(
                                        {
                                                'background': $(this).data('accent'),
                                                'color': '#fff'
                                        }
                                );
                        }
                );
                $(document.body).on(
                        'click',
                        '.epl-contact-all-tags-container > span .epl-tag-btn',
                        function (e) {
                                par = $(this).parent();
                                e.preventDefault();
                                if ($(this).parent().hasClass('epl-lock')) {
                                        return false;
                                }
                                par.siblings().removeClass('epl-lock').find('.epl-popup-box').remove();
                                par.siblings().find('.epl-tag-btn').trigger('mouseleave');
                                par.toggleClass('epl-lock');
                                var tpl = '<div class="epl-popup-box epl-all-tags-single-tag">\n    <span class="epl-single-tag-name">\n        <input type="text" id="epl-single-tag-name-input">\n    </span>\n    <ul class="epl-colors-list" >\n        <li class="epl-color-gray" data-color="#d2d2d2"><b></b></li>\n        <li class="epl-color-green" data-color="#43ac6d"><b></b></li>\n        <li class="epl-color-blue" data-color="#459bc4"><b></b></li>\n        <li class="epl-color-orange" data-color="#f9845b"><b></b></li>\n        <li class="epl-color-purple" data-color="#7d669e"><b></b></li>\n        <li class="epl-color-red" data-color="#ed5a5a"><b></b></li>\n        <li class="epl-color-yellow" data-color="#ecc33f"><b></b></li>\n    </ul>\n    <div class="epl-single-tag-opts">\n        <span data-action="filter" title="Filter" class="dashicons dashicons-filter"></span>\n        <span data-action="delete" title="Trash" class="dashicons dashicons-trash"></span>\n    </div>\n</div>';
                                if ($(this).find('.epl-popup-box').length != 1) {
                                        $(tpl).appendTo(par).slideDown();
                                }
                        }
                );

                var EPL_Contact_Tags = {

                        init: function () {
                                this.update_bg();
                                this.update_name();
                                this.delete();
                                this.filter();
                        },

                        update_bg: function () {
                                $(document.body).on(
                                        'click',
                                        '.epl-all-tags-single-tag .epl-colors-list  li',
                                        function (e) {
                                                e.preventDefault();
                                                var _this = $(this);
                                                $(this).siblings().removeClass('active');
                                                $(this).addClass('active');

                                                var data = {
                                                        action: "contact_tags_update",
                                                        term_id: $(this).closest('.epl-lock').find('.epl-tag-btn').data('id'),
                                                        bg: $(this).data('color'),
                                                        '_epl_nonce': epl_admin_vars.ajax_nonce
                                                }
                                                $.ajax(
                                                        {
                                                                type: "POST",
                                                                data: data,
                                                                url: ajaxurl,
                                                                success: function (success) {
                                                                        if (success) {
                                                                                _this.closest('.epl-lock').find('.epl-tag-btn').data('accent', _this.data('color'));
                                                                                _this.closest('.epl-lock').find('.epl-tag-btn').css('background', _this.data('color'));
                                                                        }

                                                                }
                                                        }
                                                );
                                        }
                                );
                        },
                        update_name: function () {
                                $(document.body).on(
                                        'blur',
                                        '#epl-single-tag-name-input',
                                        function (e) {
                                                e.preventDefault();
                                                var _this = $(this);

                                                var data = {
                                                        action: "contact_tags_update",
                                                        term_id: $(this).closest('.epl-lock').find('.epl-tag-btn').data('id'),
                                                        label: $(this).val(),
                                                        '_epl_nonce': epl_admin_vars.ajax_nonce
                                                }
                                                $.ajax(
                                                        {
                                                                type: "POST",
                                                                data: data,
                                                                url: ajaxurl,
                                                                success: function (success) {
                                                                        if (success) {
                                                                                _this.closest('.epl-lock').find('.epl-tag-btn').text(_this.val());
                                                                        }

                                                                }
                                                        }
                                                );
                                        }
                                );
                        },
                        delete: function () {
                                $(document.body).on(
                                        'click',
                                        '.epl-single-tag-opts .dashicons-trash',
                                        function (e) {
                                                e.preventDefault();
                                                var _this = $(this);

                                                var data = {
                                                        action: "contact_tags_update",
                                                        term_id: $(this).closest('.epl-lock').find('.epl-tag-btn').data('id'),
                                                        delete: 1,
                                                        '_epl_nonce': epl_admin_vars.ajax_nonce
                                                }
                                                $.ajax(
                                                        {
                                                                type: "POST",
                                                                data: data,
                                                                url: ajaxurl,
                                                                success: function (success) {
                                                                        if (success) {
                                                                                _this.closest('.epl-lock').fadeOut().remove();
                                                                        }

                                                                }
                                                        }
                                                );
                                        }
                                );
                        },
                        filter: function () {
                                $(document.body).on(
                                        'click',
                                        '.epl-single-tag-opts .dashicons-filter',
                                        function (e) {
                                                e.preventDefault();
                                                var tag_filter = $(this).closest('.epl-lock').find('.epl-tag-btn').data('id');
                                                window.location.href = 'admin.php?page=epl-contacts&tag_filter=' + tag_filter;
                                        }
                                );
                        },
                }

                EPL_Contact_Tags.init();

                /**
                * Display hidden fields if enabled
                *
                * @since 3.5.0
                */
                if ( '1' == epl_admin_vars.display_hidden_fields ) {

                        $('.epl-field-type-hidden').each(function () {
                                $(this).css('display', 'inline-block');
                                $(this).find('input,select').show();
                        });
                }

                /**
                * Display google map error
                * @since 3.5.0
                */

                if ($('#property_address_coordinates').length) {

                        if ( epl_admin_vars.google_api_key == '' && epl_admin_vars.google_map_disabled != 'on' ) {
                                $('#epl_property_address_coordinates').after('<div class="epl-danger epl-warning-map-key"><p>' + epl_admin_vars.google_api_error + '</p></div>');
                        }
                }
        }


);

function epl_generate_inspection_markup() {
        var tpl;
        var Year = new Date().getFullYear();
        tpl = '<div class="epl-added-inspection"></div><div id="epl-inspection-markup" class="epl-inspection-markup">';
        tpl += '<input type="text" style="width:6em;" autocomplete="off" id="epl-inspection-date" maxlength="2" size="2" placeholder="01"> ';
        tpl += ' From ';
        tpl += '<input class="validate[custom[onlyNumber]]" type="text" autocomplete="off" id="epl-inspection-start-hh" maxlength="2" size="2" placeholder="01">';
        tpl += ':<input type="text" autocomplete="off" id="epl-inspection-start-mm" maxlength="2" size="2" placeholder="01"> ';
        tpl += '<select id="epl-inspection-start-ampm" class="epl-inspection-ampm">';
        tpl += '<option value="AM">AM</option>';
        tpl += '<option value="PM">PM</option>';
        tpl += '</select>';
        tpl += ' | To ';
        tpl += '<input class="validate[custom[onlyNumber]]" type="text" autocomplete="off" id="epl-inspection-end-hh" maxlength="2" size="2" placeholder="01">';
        tpl += ':<input class="validate[custom[onlyNumber]]" type="text" autocomplete="off" id="epl-inspection-end-mm" maxlength="2" size="2" placeholder="01"> ';

        tpl += '<select id="epl-inspection-end-ampm" class="epl-inspection-ampm">';
        tpl += '<option value="AM">AM</option>';
        tpl += '<option value="PM">PM</option>';
        tpl += '</select>';

        tpl += '<a id="epl-inspection-add" class="button">Add</a></div>';
        return tpl;
}
