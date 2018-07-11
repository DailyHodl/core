<?php

namespace MailOptin\Core\Admin\Customizer\EmailCampaign;

use MailOptin\Core\Admin\Customizer\CustomControls\ControlsHelpers;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Chosen_Select_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Controls_Tab_Toggle;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Content;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Input_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Email_Schedule_Time_Fields_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Range_Value_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Repositories\ConnectionsRepository;
use MailOptin\Core\Repositories\EmailCampaignRepository;

class CustomizerControls
{
    /** @var \WP_Customize_Manager */
    private $wp_customize;

    /** @var Customizer */
    private $customizerClassInstance;

    /** @var string DB option name prefix */
    private $option_prefix;

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function __construct($wp_customize, $option_prefix, $customizerClassInstance)
    {
        $this->wp_customize = $wp_customize;
        $this->customizerClassInstance = $customizerClassInstance;
        $this->option_prefix = $option_prefix;

        $this->selective_control_modifications();

        add_action('customize_controls_print_footer_scripts', function () {
            ?>
            <script type="text/javascript">
                var mailoptin_tab_control_config = <?php echo wp_json_encode($this->tab_toggle_controls_config());?>;
            </script>
            <?php
        });
    }

    public function tab_toggle_controls_config()
    {
        return apply_filters('mailoptin_email_campaign_tab_toggle_config',
            [
                'general' => apply_filters('mailoptin_email_campaign_tab_toggle_general_config', [
                    'footer_removal',
                    'footer_copyright_line',
                    'footer_description',
                    'footer_unsubscribe_line',
                    'footer_unsubscribe_link_label'
                ]),
                'style' => apply_filters('mailoptin_email_campaign_tab_toggle_design_config', [
                    'footer_background_color',
                    'footer_text_color',
                    'footer_font_size',
                    'footer_unsubscribe_link_color'
                ]),
            ]);
    }

    /**
     * All code, filer, action to make modification to a control will go here.
     */
    public function selective_control_modifications()
    {
        add_filter('mailoptin_customizer_settings_email_campaign_subject_description',
            function ($description, $campaign_type) {
                if (EmailCampaignRepository::NEW_PUBLISH_POST == $campaign_type) {
                    $description = sprintf(
                        __('Available placeholders for use in subject line:%s %s', 'mailoptin'),
                        '<br><strong>{{title}}</strong>:',
                        __(' title of new published post.', 'mailoptin')
                    );
                }

                return $description;
            }, 10, 2);
    }

    public function campaign_settings_controls()
    {
        $saved_connection_service = EmailCampaignRepository::get_customizer_value(
            $this->customizerClassInstance->email_campaign_id,
            'connection_service'
        );

        // prepend 'Select...' to the array of email list.
        // because select control will be hidden if no choice is found.
        $connection_email_list = ['' => __('Select...', 'mailoptin')] + ConnectionsRepository::connection_email_list($saved_connection_service);

        $campaign_type = $this->customizerClassInstance->email_campaign_type;

        $campaign_settings_controls = array(
            'email_campaign_subject' => new WP_Customize_Custom_Input_Control(
                $this->wp_customize,
                'email_campaign_subject',
                apply_filters('mailoptin_customizer_settings_campaign_subject_args', array(
                        'label' => __('Email Campaign Subject', 'mailoptin'),
                        'section' => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => $this->option_prefix . '[email_campaign_subject]',
                        'description' => __('Enter a subject or title for this automation email campaigns.', 'mailoptin'),
                        'sub_description' => apply_filters('mailoptin_customizer_settings_email_campaign_subject_description', __('Subject of email campaign.', 'mailoptin'),
                            $campaign_type
                        ),
                        'priority' => 20
                    )
                )
            ),
            'post_categories' => new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[post_categories]',
                apply_filters('mo_optin_form_customizer_post_categories_args', array(
                        'label' => __('Restrict to selected categories', 'mailoptin'),
                        'section' => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => $this->option_prefix . '[post_categories]',
                        'choices' => ControlsHelpers::get_categories(),
                        'priority' => 45
                    )
                )
            ),
            'post_tags' => new WP_Customize_Chosen_Select_Control(
                $this->wp_customize,
                $this->option_prefix . '[post_tags]',
                apply_filters('mo_optin_form_customizer_post_tags_args', array(
                        'label' => __('Restrict to selected tags', 'mailoptin'),
                        'section' => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => $this->option_prefix . '[post_tags]',
                        'choices' => ControlsHelpers::get_tags(),
                        'priority' => 48
                    )
                )
            ),
            'connection_service' => apply_filters('mailoptin_customizer_settings_campaign_connection_service_args',
                array(
                    'type' => 'select',
                    'label' => __('Select Connection', 'mailoptin'),
                    'section' => $this->customizerClassInstance->campaign_settings_section_id,
                    'settings' => $this->option_prefix . '[connection_service]',
                    'choices' => ConnectionsRepository::get_connections('email_campaign'),
                    'description' => __('Choose the email service or connection that newsletter will be sent to.', 'mailoptin'),
                    'priority' => 50
                )
            ),
            'connection_email_list' => apply_filters('mailoptin_customizer_settings_campaign_connection_email_list_args',
                array(
                    'type' => 'select',
                    'label' => __('Select Email List', 'mailoptin'),
                    'section' => $this->customizerClassInstance->campaign_settings_section_id,
                    'settings' => $this->option_prefix . '[connection_email_list]',
                    'choices' => $connection_email_list,
                    'description' => __('Choose the specific email list that email campaign will be sent to.', 'mailoptin'),
                    'priority' => 60
                )
            ),
            'send_immediately' => new WP_Customize_Toggle_Control(
                $this->wp_customize,
                $this->option_prefix . '[send_immediately]',
                apply_filters('mailoptin_customizer_settings_campaign_send_immediately_args', array(
                        'label' => __('Send Immediately', 'mailoptin'),
                        'section' => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => $this->option_prefix . '[send_immediately]',
                        'description' => __('Check to enable sending of "new post newsletter" immediately after publication.', 'mailoptin'),
                        'priority' => 70,
                    )
                )
            ),
            'email_campaign_schedule' => new WP_Customize_Email_Schedule_Time_Fields_Control(
                $this->wp_customize,
                $this->option_prefix . '[email_campaign_schedule]',
                apply_filters('mailoptin_customizer_settings_campaign_schedule_args', array(
                        'label' => __('Schedule Email Campaign', 'mailoptin'),
                        'section' => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => [
                            'schedule_digit' => $this->option_prefix . '[schedule_digit]',
                            'schedule_type' => $this->option_prefix . '[schedule_type]'
                        ],
                        // specify the kind of input field
                        'type' => 'text',
                        'input_attrs' => [
                            'size' => 2,
                            'maxlength' => 2,
                            'style' => 'width:auto',
                            'pattern' => '([0-9]){2}'
                        ],
                        'select_attrs' => ['style' => 'width:auto'],
                        'select_choices' => [
                            'minutes' => __('Minutes', 'mailoptin'),
                            'hours' => __('Hours', 'mailoptin'),
                            'days' => __('Days', 'mailoptin'),
                        ],
                        'description' => apply_filters('mailoptin_customizer_settings_email_campaign_schedule_description', __('Configure when email campaign will be sent out after post publication. Example: setting the input field to "5", and selecting "hours" will send the email 5 hours after post publication.', 'mailoptin'), $campaign_type),
                        'priority' => 80
                    )
                )
            ),
            'ajax_nonce' => apply_filters('mailoptin_customizer_settings_campaign_ajax_nonce_args', array(
                    'type' => 'hidden',
                    // simple hack because control won't render if label is empty.
                    'label' => '&nbsp;',
                    'section' => $this->customizerClassInstance->campaign_settings_section_id,
                    'settings' => $this->option_prefix . '[ajax_nonce]',
                    // 999 cos we want it to be bottom.
                    'priority' => 999,
                )
            )
        );

        if (!defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            unset($campaign_settings_controls['post_categories']);
            unset($campaign_settings_controls['post_tags']);
            $content = sprintf(
                __('Upgrade to %sMailOptin Premium%s to restrict by post categories, tags and to send email campaigns directly to your list in MailChimp, Campaign Monitor, Aweber, Constant Contact, Drip, MailerLite, ConvertKit etc.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=new_post_campaign_settings">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            // always prefix with the name of the connect/connection service.
            $campaign_settings_controls['email_campaign_settings_notice'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[email_campaign_settings_notice]',
                apply_filters('mo_optin_form_customizer_email_campaign_settings_notice_args', array(
                        'content' => $content,
                        'section' => $this->customizerClassInstance->campaign_settings_section_id,
                        'settings' => $this->option_prefix . '[email_campaign_settings_notice]',
                        'priority' => 45,
                    )
                )
            );
        }

        $email_campaign_settings_control_args = apply_filters(
            "mailoptin_email_campaign_customizer_settings_controls",
            $campaign_settings_controls,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_email_campaign_settings_controls',
            $email_campaign_settings_control_args,
            $campaign_type,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($email_campaign_settings_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_email_campaign_settings_controls',
            $email_campaign_settings_control_args,
            $campaign_type,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );
    }

    public function page_controls()
    {
        $page_control_args = apply_filters(
            "mailoptin_template_customizer_page_controls",
            array(
                'page_background_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[page_background_color]',
                    apply_filters('mailoptin_template_customizer_background_color_args', array(
                            'label' => __('Background Color', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_page_section_id,
                            'settings' => $this->option_prefix . '[page_background_color]',
                            'priority' => 10
                        )
                    )
                ),
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );


        if (!defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $content = sprintf(
                __('Upgrade to %sMailOptin Premium%s to access the Custom CSS feature that will allow you customize this template to your heart content.', 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=email_automation_custom_css_upgrade">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            // always prefix with the name of the connect/connection service.
            $page_control_args['custom_css_upgrade_notice'] = new WP_Customize_Custom_Content(
                $this->wp_customize,
                $this->option_prefix . '[custom_css_upgrade_notice]',
                apply_filters('mo_optin_form_customizer_custom_css_upgrade_notice_args', array(
                        'content' => $content,
                        'section' => $this->customizerClassInstance->campaign_page_section_id,
                        'settings' => $this->option_prefix . '[custom_css_upgrade_notice]',
                        'priority' => 20,
                    )
                )
            );
        }

        do_action('mailoptin_before_page_controls_addition',
            $page_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($page_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_page_controls_addition',
            $page_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );
    }

    public function header_controls()
    {
        $header_control_args = apply_filters(
            "mailoptin_template_customizer_header_controls",
            array(
                'header_removal' => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[header_removal]',
                    apply_filters('mailoptin_template_customizer_header_removal_args', array(
                            'label' => esc_html__('Remove Header', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_header_section_id,
                            'settings' => $this->option_prefix . '[header_removal]',
                            'type' => 'light',// light, ios, flat
                            'priority' => 10
                        )
                    )
                ),
                'header_logo' => new \WP_Customize_Cropped_Image_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[header_logo]',
                    apply_filters('mailoptin_template_customizer_header_logo_args', array(
                            'label' => __('Logo', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_header_section_id,
                            'settings' => $this->option_prefix . '[header_logo]',
                            'flex_width' => true,
                            'flex_height' => true,
                            'button_labels' => array(
                                'select' => __('Select Logo', 'mailoptin'),
                                'change' => __('Change Logo', 'mailoptin'),
                                'default' => __('Default', 'mailoptin'),
                                'remove' => __('Remove', 'mailoptin'),
                                'placeholder' => __('No logo selected', 'mailoptin'),
                                'frame_title' => __('Select Logo', 'mailoptin'),
                                'frame_button' => __('Choose Logo', 'mailoptin'),
                            ),
                            'priority' => 20
                        )
                    )
                ),
                'header_background_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[header_background_color]',
                    apply_filters('mailoptin_template_customizer_header_background_color_args', array(
                            'label' => __('Background Color', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_header_section_id,
                            'settings' => $this->option_prefix . '[header_background_color]',
                            'priority' => 30
                        )
                    )
                ),
                'header_text_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[header_text_color]',
                    apply_filters('mailoptin_template_customizer_header_text_color_args', array(
                            'label' => __('Text Color', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_header_section_id,
                            'settings' => $this->option_prefix . '[header_text_color]',
                            'priority' => 40
                        )
                    )
                ),
                'header_text' => apply_filters('mailoptin_template_customizer_header_text_args',
                    array(
                        'label' => __('Header Text', 'mailoptin'),
                        'description' => __('This is used when template logo is not set.', 'mailoptin'),
                        'section' => $this->customizerClassInstance->campaign_header_section_id,
                        'type' => 'text',
                        'settings' => $this->option_prefix . '[header_text]',
                        'priority' => 50
                    )
                ),
                'header_web_version_link_label' => apply_filters('mailoptin_template_customizer_header_web_version_link_label_args',
                    array(
                        'label' => __('Web Version Link Label', 'mailoptin'),
                        'type' => 'text',
                        'section' => $this->customizerClassInstance->campaign_header_section_id,
                        'settings' => $this->option_prefix . '[header_web_version_link_label]',
                        'priority' => 60
                    )
                ),
                'header_web_version_link_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[header_web_version_link_color]',
                    apply_filters('mailoptin_template_customizer_header_web_version_link_color_args', array(
                            'label' => __('Web Version Link Color', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_header_section_id,
                            'settings' => $this->option_prefix . '[header_web_version_link_color]',
                            'priority' => 70
                        )
                    )
                ),
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_header_controls_addition',
            $header_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($header_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_header_controls_addition',
            $header_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

    }


    public function content_controls()
    {
        $content_control_args = apply_filters(
            "mailoptin_template_customizer_content_controls",
            array(
                'content_background_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_background_color]',
                    apply_filters('mailoptin_template_customizer_content_background_color_args', array(
                            'label' => __('Background Color', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_background_color]',
                            'priority' => 10
                        )
                    )
                ),
                'content_text_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_text_color]',
                    apply_filters('mailoptin_template_customizer_content_text_color_args', array(
                            'label' => __('Text Color', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_text_color]',
                            'priority' => 20
                        )
                    )
                ),
                'content_remove_feature_image' => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_remove_feature_image]',
                    apply_filters('mailoptin_template_customizer_content_remove_feature_image_args', array(
                            'label' => esc_html__('Remove Featured Image', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_remove_feature_image]',
                            'priority' => 30
                        )
                    )
                ),
                'default_image_url' => apply_filters('mailoptin_customizer_settings_campaign_default_image_url_args',
                    array(
                        'type' => 'text',
                        'label' => __('Fallback Featured Image', 'mailoptin'),
                        'section' => $this->customizerClassInstance->campaign_content_section_id,
                        'settings' => $this->option_prefix . '[default_image_url]',
                        'description' => __('Enter URL of an image to use when a post lack a feature image.', 'mailoptin'),
                        'priority' => 40
                    )
                ),
                'post_content_length' => apply_filters('mailoptin_customizer_settings_campaign_post_content_length_args',
                    array(
                        'type' => 'number',
                        'label' => __('Post Content Length', 'mailoptin'),
                        'section' => $this->customizerClassInstance->campaign_content_section_id,
                        'settings' => $this->option_prefix . '[post_content_length]',
                        'description' => __('Enter the number of words to limit the post content to. Set to "0" for full post content. Default is 150.', 'mailoptin'),
                        'priority' => 50
                    )
                ),
                'content_title_font_size' => new WP_Customize_Range_Value_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_title_font_size]',
                    apply_filters('mailoptin_template_customizer_content_title_font_size_args', array(
                            'label' => __('Title Font Size', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_title_font_size]',
                            'input_attrs' => array(
                                'min' => 10,
                                'max' => 50,
                                'step' => 1,
                                'suffix' => 'px', //optional suffix
                            ),
                            'priority' => 60
                        )
                    )
                ),
                'content_body_font_size' => new WP_Customize_Range_Value_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_body_font_size]',
                    apply_filters('mailoptin_template_customizer_content_body_font_size_args', array(
                            'label' => __('Body Font Size', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_body_font_size]',
                            'input_attrs' => array(
                                'min' => 10,
                                'max' => 50,
                                'step' => 1,
                                'suffix' => 'px'
                            ),
                            'priority' => 80
                        )
                    )
                ),
                'content_alignment' => array(
                    'label' => __('Content Alignment', 'mailoptin'),
                    'section' => $this->customizerClassInstance->campaign_content_section_id,
                    'settings' => $this->option_prefix . '[content_alignment]',
                    'type' => 'select',
                    'choices' => array(
                        'left' => __('Left', 'mailoptin'),
                        'center' => __('Center', 'mailoptin'),
                        'right' => __('Right', 'mailoptin'),
                    ),
                    'priority' => 100
                ),
                'content_remove_ellipsis_button' => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_remove_ellipsis_button]',
                    apply_filters('mailoptin_template_customizer_content_remove_ellipsis_button_args', array(
                            'label' => esc_html__('Remove Ellipsis Button', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_content_section_id,
                            'settings' => $this->option_prefix . '[content_remove_ellipsis_button]',
                            'type' => 'light',// light, ios, flat
                            'priority' => 120
                        )
                    )
                ),
                'content_ellipsis_button_alignment' => array(
                    'label' => __('Ellipsis Button Alignment', 'mailoptin'),
                    'section' => $this->customizerClassInstance->campaign_content_section_id,
                    'settings' => $this->option_prefix . '[content_ellipsis_button_alignment]',
                    'type' => 'select',
                    'choices' => array(
                        'left' => __('Left', 'mailoptin'),
                        'center' => __('Center', 'mailoptin'),
                        'right' => __('Right', 'mailoptin'),
                    ),
                    'priority' => 140
                ),
                'content_ellipsis_button_background_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_ellipsis_button_background_color]',
                    array(
                        'label' => __('Ellipsis Button Background Color', 'mailoptin'),
                        'section' => $this->customizerClassInstance->campaign_content_section_id,
                        'settings' => $this->option_prefix . '[content_ellipsis_button_background_color]',
                        'priority' => 160
                    )
                ),
                'content_ellipsis_button_text_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[content_ellipsis_button_text_color]',
                    array(
                        'label' => __('Ellipsis Button Text Color', 'mailoptin'),
                        'section' => $this->customizerClassInstance->campaign_content_section_id,
                        'settings' => $this->option_prefix . '[content_ellipsis_button_text_color]',
                        'priority' => 180
                    )
                ),
                'content_ellipsis_button_label' => array(
                    'label' => __('Ellipsis Button Label', 'mailoptin'),
                    'type' => 'text',
                    'section' => $this->customizerClassInstance->campaign_content_section_id,
                    'settings' => $this->option_prefix . '[content_ellipsis_button_label]',
                    'priority' => 200
                ),
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_content_controls_addition',
            $content_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($content_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_content_controls_addition',
            $content_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

    }

    public function footer_controls()
    {
        $footer_control_args = apply_filters(
            "mailoptin_template_customizer_footer_controls",
            array(
                'footer_controls_tab_toggle' => new WP_Customize_Controls_Tab_Toggle(
                    $this->wp_customize,
                    $this->option_prefix . '[footer_controls_tab_toggle]',
                    apply_filters('mailoptin_template_customizer_footer_controls_tab_toggle_args', array(
                            'section' => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_controls_tab_toggle]',
                            'priority' => 2
                        )
                    )
                ),
                'footer_removal' => new WP_Customize_Toggle_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[footer_removal]',
                    apply_filters('mailoptin_template_customizer_footer_removal_args', array(
                            'label' => esc_html__('Remove Footer', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_removal]',
                            'type' => 'light',// light, ios, flat
                            'priority' => 10
                        )
                    )
                ),
                'footer_background_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[footer_background_color]',
                    apply_filters('mailoptin_template_customizer_footer_background_color_args', array(
                            'label' => __('Background Color', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_background_color]',
                            'priority' => 20
                        )
                    )
                ),
                'footer_text_color' => new \WP_Customize_Color_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[footer_text_color]',
                    apply_filters('mailoptin_template_customizer_footer_text_color_args', array(
                            'label' => __('Text Color', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_text_color]',
                            'priority' => 30
                        )
                    )
                ),
                'footer_font_size' => new WP_Customize_Range_Value_Control(
                    $this->wp_customize,
                    $this->option_prefix . '[footer_font_size]',
                    apply_filters('mailoptin_template_customizer_footer_font_size_args', array(
                            'label' => __('Footer Font Size', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_font_size]',
                            'input_attrs' => array(
                                'min' => 10,
                                'max' => 40,
                                'step' => 1,
                                'suffix' => 'px'
                            ),
                            'priority' => 40
                        )
                    )
                ),
                'footer_copyright_line' => apply_filters('mailoptin_template_customizer_footer_copyright_line_args',
                    array(
                        'label' => __('Copyright Line', 'mailoptin'),
                        'type' => 'text',
                        'section' => $this->customizerClassInstance->campaign_footer_section_id,
                        'settings' => $this->option_prefix . '[footer_copyright_line]',
                        'priority' => 50
                    )
                ),
                'footer_description' => apply_filters('mailoptin_template_customizer_footer_description_args',
                    array(
                        'label' => __('Mailing Address', 'mailoptin'),
                        'type' => 'textarea',
                        'section' => $this->customizerClassInstance->campaign_footer_section_id,
                        'settings' => $this->option_prefix . '[footer_description]',
                        'priority' => 60
                    )
                ),
                'footer_unsubscribe_line' => apply_filters('mailoptin_template_customizer_footer_unsubscribe_line_args',
                    array(
                        'label' => __('Unsubscribe Line', 'mailoptin'),
                        'type' => 'text',
                        'section' => $this->customizerClassInstance->campaign_footer_section_id,
                        'settings' => $this->option_prefix . '[footer_unsubscribe_line]',
                        'priority' => 70
                    )
                ),
                'footer_unsubscribe_link_label' => apply_filters('mailoptin_template_customizer_footer_unsubscribe_link_color_args',
                    array(
                        'label' => __('Unsubscribe Link Label', 'mailoptin'),
                        'type' => 'text',
                        'section' => $this->customizerClassInstance->campaign_footer_section_id,
                        'settings' => $this->option_prefix . '[footer_unsubscribe_link_label]',
                        'priority' => 80
                    )
                ),
                'footer_unsubscribe_link_color' => apply_filters('mailoptin_template_customizer_footer_unsubscribe_link_color_args',
                    new \WP_Customize_Color_Control(
                        $this->wp_customize,
                        $this->option_prefix . '[footer_unsubscribe_link_color]',
                        array(
                            'label' => __('Unsubscribe Link Color', 'mailoptin'),
                            'section' => $this->customizerClassInstance->campaign_footer_section_id,
                            'settings' => $this->option_prefix . '[footer_unsubscribe_link_color]',
                            'priority' => 90
                        )
                    )
                )
            ),
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        do_action('mailoptin_before_footer_controls_addition',
            $footer_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

        foreach ($footer_control_args as $id => $args) {
            if (is_object($args)) {
                $this->wp_customize->add_control($args);
            } else {
                $this->wp_customize->add_control($this->option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_footer_controls_addition',
            $footer_control_args,
            $this->wp_customize,
            $this->option_prefix,
            $this->customizerClassInstance
        );

    }
}