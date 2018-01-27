(function (api, $) {
    'use strict';

    $(window).on('load', function () {

        function name_field_control_toggle(is_display_optin_fields) {

            api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][hide_name_field]', function (setting) {
                var is_hide_name_field, linkSettingValueToControlActiveState;

                /**
                 * Determine whether the hide name dependent fields should be displayed.
                 *
                 * @returns {boolean} Is displayed?
                 */
                is_hide_name_field = function () {
                    return is_display_optin_fields === true && !setting.get();
                };

                /**
                 * Update a control's active state according to the header_textcontrol setting's value.
                 *
                 * @param {wp.customize.Control} control Site Title or Tagline Control.
                 */
                linkSettingValueToControlActiveState = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_hide_name_field());
                    };

                    // FYI: With the following we can eliminate all of our PHP active_callback code.
                    control.active.validate = is_hide_name_field;

                    // Set initial active state.
                    setActiveState();

                    /*
                     * Update activate state whenever the setting is changed.
                     * Even when the setting does have a refresh transport where the
                     * server-side active callback will manage the active state upon
                     * refresh, having this JS management of the active state will
                     * ensure that controls will have their visibility toggled
                     * immediately instead of waiting for the preview to load.
                     * This is especially important if the setting has a postMessage
                     * transport where changing the setting wouldn't normally cause
                     * the preview to refresh and thus the server-side active_callbacks
                     * would not get invoked.
                     */
                    setting.bind(setActiveState);
                };

                // Call linkSettingValueToControlActiveState on the site title and tagline controls when they exist.
                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][name_field_header]', linkSettingValueToControlActiveState);
                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][name_field_placeholder]', linkSettingValueToControlActiveState);
                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][name_field_color]', linkSettingValueToControlActiveState);
                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][name_field_background]', linkSettingValueToControlActiveState);
                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][name_field_font]', linkSettingValueToControlActiveState);
            });
        }

        function cta_button_action_toggle(is_show_cta_fields) {

            api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_action]', function (setting) {
                var is_displayed, linkSettingValueToControlActiveState;

                is_displayed = function () {
                    return is_show_cta_fields === true && setting.get() === 'navigate_to_url';
                };

                linkSettingValueToControlActiveState = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_displayed());
                    };

                    control.active.validate = is_displayed;

                    // Set initial active state.
                    setActiveState();

                    setting.bind(setActiveState);
                };

                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_navigation_url]', linkSettingValueToControlActiveState);
            });
        }

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][display_only_button]', function (setting) {
            var is_display_optin_fields, is_show_cta_fields, callToActionFieldsToggle, optinFieldsDisplayToggle;

            is_display_optin_fields = function () {
                return !setting.get();
            };

            is_show_cta_fields = function () {
                return setting.get();
            };

            optinFieldsDisplayToggle = function (control) {
                var setActiveState = function () {
                    control.active.set(is_display_optin_fields());
                    name_field_control_toggle(is_display_optin_fields());
                };

                control.active.validate = is_display_optin_fields;

                // Set initial active state.
                setActiveState();

                setting.bind(setActiveState);
            };

            callToActionFieldsToggle = function (control) {
                var setActiveState = function () {
                    control.active.set(is_show_cta_fields());
                    cta_button_action_toggle(is_show_cta_fields());
                };

                control.active.validate = is_show_cta_fields;

                // Set initial active state.
                setActiveState();

                setting.bind(setActiveState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][name_field_header]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][email_field_header]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][submit_button_header]', optinFieldsDisplayToggle);

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][hide_name_field]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][name_field_placeholder]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][name_field_color]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][name_field_background]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][name_field_font]', optinFieldsDisplayToggle);

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][email_field_placeholder]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][email_field_color]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][email_field_background]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][email_field_font]', optinFieldsDisplayToggle);

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][submit_button]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][submit_button_color]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][submit_button_background]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][submit_button_font]', optinFieldsDisplayToggle);

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_header]', callToActionFieldsToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_action]', callToActionFieldsToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button]', callToActionFieldsToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_color]', callToActionFieldsToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_background]', callToActionFieldsToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_font]', callToActionFieldsToggle);
        });

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][bar_position]', function (setting) {
            var is_displayed, linkSettingValueToControlActiveState;

            is_displayed = function () {
                return setting.get() == 'top';
            };

            linkSettingValueToControlActiveState = function (control) {
                var setActiveState = function () {
                    control.active.set(is_displayed());
                };

                control.active.validate = is_displayed;

                // Set initial active state.
                setActiveState();

                setting.bind(setActiveState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][bar_sticky]', linkSettingValueToControlActiveState);
        });

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][click_launch_status]', function (setting) {
            var is_displayed, linkSettingValueToControlActiveState;

            is_displayed = function () {
                return setting.get();
            };

            linkSettingValueToControlActiveState = function (control) {
                var setActiveState = function () {
                    control.active.set(is_displayed());
                    // hide all display rules sections save for click launch when click launch is activated.
                    api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][load_optin_globally]').active(!is_displayed());
                    api.section('mo_wp_exit_intent_display_rule_section').active(!is_displayed());
                    api.section('mo_wp_x_seconds_display_rule_section').active(!is_displayed());
                    api.section('mo_wp_x_scroll_display_rule_section').active(!is_displayed());
                    api.section('mo_wp_x_page_views_display_rule_section').active(!is_displayed());
                    api.section('mo_wp_page_filter_display_rule_section').active(!is_displayed());
                    api.section('mo_wp_shortcode_template_tag_display_rule_section').active(!is_displayed());
                };

                control.active.validate = is_displayed;

                // Set initial active state.
                setActiveState();

                setting.bind(setActiveState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][click_launch_basic_shortcode]', linkSettingValueToControlActiveState);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][click_launch_advance_shortcode]', linkSettingValueToControlActiveState);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][click_launch_html_code]', linkSettingValueToControlActiveState);
        });

        // contextual display of redirect_url in success panel/section.
        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][success_action]', function (setting) {
                var is_redirect_url_value_displayed, is_success_message_config_link_displayed,
                    linkSettingValueToControlActiveState1, linkSettingValueToControlActiveState2;

                is_success_message_config_link_displayed = function () {
                    return setting.get() === 'success_message';
                };

                is_redirect_url_value_displayed = function () {
                    return setting.get() === 'redirect_url';
                };

                linkSettingValueToControlActiveState1 = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_redirect_url_value_displayed());
                    };

                    control.active.validate = is_redirect_url_value_displayed;
                    // Set initial active state.
                    setActiveState();

                    setting.bind(setActiveState);
                };

                linkSettingValueToControlActiveState2 = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_success_message_config_link_displayed());
                    };

                    control.active.validate = is_success_message_config_link_displayed;
                    // Set initial active state.
                    setActiveState();

                    setting.bind(setActiveState);
                };

                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][redirect_url_value]', linkSettingValueToControlActiveState1);
                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][pass_lead_data_redirect_url]', linkSettingValueToControlActiveState1);
                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][success_message_config_link]', linkSettingValueToControlActiveState2);
            }
        );

        // handles click to select on input readonly fields
        $('.mo-click-select').click(function () {
            this.select();
        });

        // handles activation and deactivation of optin
        $('#mo-optin-activate-switch').on('change', function () {
            $.post(ajaxurl, {
                action: 'mailoptin_optin_toggle_active',
                id: mailoptin_optin_campaign_id,
                status: this.checked,
                security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
            }, function ($response) {
                // do nothing
            });
        });
    });

})(wp.customize, jQuery);