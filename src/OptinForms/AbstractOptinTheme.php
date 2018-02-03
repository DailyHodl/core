<?php

namespace MailOptin\Core\OptinForms;


use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

abstract class AbstractOptinTheme extends AbstractOptinForm
{
    public function __construct($optin_campaign_id, $wp_customize = null)
    {
        add_shortcode('mo-optin-form-wrapper', [$this, 'shortcode_optin_form_wrapper']);
        add_shortcode('mo-optin-form-fields-wrapper', [$this, 'shortcode_optin_form_fields_wrapper']);
        add_shortcode('mo-optin-form-cta-wrapper', [$this, 'shortcode_optin_form_cta_wrapper']);
        add_shortcode('mo-optin-form-headline', [$this, 'shortcode_optin_form_headline']);
        add_shortcode('mo-close-optin', [$this, 'shortcode_optin_form_close_button']);
        add_shortcode('mo-optin-form-image', [$this, 'shortcode_optin_form_image']);
        add_shortcode('mo-optin-form-background-image', [$this, 'shortcode_optin_form_background_image']);
        add_shortcode('mo-optin-form-description', [$this, 'shortcode_optin_form_description']);
        add_shortcode('mo-optin-form-error', [$this, 'shortcode_optin_form_error']);
        add_shortcode('mo-optin-form-name-field', [$this, 'shortcode_optin_form_name_field']);
        add_shortcode('mo-optin-form-email-field', [$this, 'shortcode_optin_form_email_field']);
        add_shortcode('mo-optin-form-submit-button', [$this, 'shortcode_optin_form_submit_button']);
        add_shortcode('mo-optin-form-cta-button', [$this, 'shortcode_optin_form_cta_button']);
        add_shortcode('mo-optin-form-note', [$this, 'shortcode_optin_form_note']);

        do_action('mo_optin_theme_shortcodes_add', $optin_campaign_id, $wp_customize);

        parent::__construct($optin_campaign_id, $wp_customize);
    }

    /**
     * Headline styles.
     *
     * @return string
     */
    public function headline_styles()
    {
        $style_arg = apply_filters('mo_optin_form_headline_styles',
            [
                'color' => $this->get_customizer_value('headline_font_color'),
                'font-family' => $this->_construct_font_family($this->get_customizer_value('headline_font')),
            ],
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );

        if ($this->get_customizer_value('hide_headline')) {
            $style_arg['display'] = 'none';
        }

        $style = '';

        foreach ($style_arg as $key => $value) {
            if (!empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Description styles.
     *
     * @return string
     */
    public function description_styles()
    {
        $style_arg = apply_filters('mo_optin_form_description_styles',
            [
                'color' => $this->get_customizer_value('description_font_color'),
                'font-family' => $this->_construct_font_family($this->get_customizer_value('description_font'))
            ],
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );

        if ($this->get_customizer_value('hide_description')) {
            $style_arg['display'] = 'none';
        }

        $style = '';
        foreach ($style_arg as $key => $value) {
            if (!empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Note styles.
     *
     * @return string
     */
    public function note_styles()
    {
        $style = [
            'color' => $this->get_customizer_value('note_font_color'),
            'font-family' => $this->_construct_font_family($this->get_customizer_value('note_font'))
        ];

        if ($this->get_customizer_value('note_close_optin_onclick')) {
            $style['text-decoration'] = 'underline';
            $style['cursor'] = 'pointer';
        }

        $style_arg = apply_filters('mo_optin_form_note_styles', $style, $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid);

        if ($this->get_customizer_value('hide_note')) {
            $style_arg['display'] = 'none';
        }

        $style = '';

        foreach ($style_arg as $key => $value) {
            if (!empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Name field styles.
     *
     * @return string
     */
    public function name_field_styles()
    {
        $style_arg = apply_filters('mo_optin_form_name_field_styles',
            [
                'color' => $this->get_customizer_value('name_field_color'),
                'background-color' => $this->get_customizer_value('name_field_background'),
                'font-family' => $this->get_customizer_value('name_field_font'),
                'height' => 'auto'
            ],
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );

        if ($this->get_customizer_value('hide_name_field')) {
            $style_arg['display'] = 'none';
        }

        $style = '';

        foreach ($style_arg as $key => $value) {
            if (!empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Email field styles.
     *
     * @return string
     */
    public function email_field_styles()
    {
        $style_arg = apply_filters('mo_optin_form_email_field_styles',
            [
                'color' => $this->get_customizer_value('email_field_color'),
                'background-color' => $this->get_customizer_value('email_field_background'),
                'font-family' => $this->get_customizer_value('email_field_font'),
                'height' => 'auto'
            ],
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );


        $style = '';

        foreach ($style_arg as $key => $value) {
            if (!empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Submit button styles.
     *
     * @return string
     */
    public function submit_button_styles()
    {
        $style_arg = apply_filters('mo_optin_form_submit_button_styles',
            [
                'background' => $this->get_customizer_value('submit_button_background'),
                'color' => $this->get_customizer_value('submit_button_color'),
                'font-family' => $this->_construct_font_family($this->get_customizer_value('submit_button_font')),
                'height' => 'auto',
                'text-shadow' => 'none'
            ],
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );


        $style = '';

        foreach ($style_arg as $key => $value) {
            if (!empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * CTA button styles.
     *
     * @return string
     */
    public function cta_button_styles()
    {
        $style = [
            'background' => $this->get_customizer_value('cta_button_background'),
            'color' => $this->get_customizer_value('cta_button_color'),
            'font-family' => $this->_construct_font_family($this->get_customizer_value('cta_button_font')),
            'height' => 'auto',
            'text-shadow' => 'none'
        ];

        if ($this->get_customizer_value('display_only_button') !== true) {
            $style['display'] = 'none';
        }

        $style_arg = apply_filters('mo_optin_form_cta_button_styles', $style,
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );


        $style = '';

        foreach ($style_arg as $key => $value) {
            if (!empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Form container styles.
     *
     * @return string
     */
    public function form_container_styles()
    {
        $style_arg = apply_filters('mo_optin_form_container_styles',
            [
                'position' => 'relative',
                'margin-right' => 'auto',
                'margin-left' => 'auto',
                'background' => $this->get_customizer_value('form_background_color'),
                'border-color' => $this->get_customizer_value('form_border_color'),
                'line-height' => 'normal'
            ],
            $this->optin_campaign_id,
            $this->optin_campaign_type,
            $this->optin_campaign_uuid
        );


        $style = '';

        foreach ($style_arg as $key => $value) {
            if (!empty($value)) {
                $style .= "$key: $value;";
            }
        }

        return $style;
    }

    /**
     * Optin form wrapper shortcode.
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function shortcode_optin_form_wrapper($atts, $content)
    {
        $optin_campaign_uuid = $this->optin_campaign_uuid;
        $optin_css_id = $this->optin_css_id;
        $form_container_styles = $this->form_container_styles();
        $name_email_class_indicator = $this->get_customizer_value('hide_name_field') === true ? 'mo-has-email' : 'mo-has-name-email';

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-form-wrapper $name_email_class_indicator $class";

        $style = esc_html($atts['style']);
        $style = "$form_container_styles $style";

        $html = "<div id=\"$optin_css_id\" class=\"$class\" style=\"$style\">";
        $html .= apply_filters('mo_optin_form_before_form_tag', '', $this->optin_campaign_id, $this->optin_campaign_type, $optin_campaign_uuid, $optin_css_id);
        $html .= "<form method=\"post\" class='mo-optin-form' id='{$optin_css_id}_form' style='margin 0;'>";
        $html .= do_shortcode($content);

        // Don't change type from text to email to prevent "An invalid form control with name='text' is not focusable." error
        $html .= "<input id='{$this->optin_css_id}_honeypot_email_field' type='text' name='email' value='' style='display:none'/>";
        $html .= '<input id="' . $this->optin_css_id . '_honeypot_website_field" type="text" name="website" value="" style="display:none" />';

        $html .= apply_filters('mo_optin_form_before_closing_form_tag', '', $this->optin_campaign_id, $this->optin_campaign_type, $optin_campaign_uuid, $optin_css_id);
        $html .= '</form>';
        $html .= apply_filters('mo_optin_form_after_form_tag', '', $this->optin_campaign_id, $this->optin_campaign_type, $optin_campaign_uuid, $optin_css_id);

        $html .= $this->processing_success_structure();

        if (!$this->customizer_defaults['mo_optin_branding_outside_form'] && $this->optin_campaign_type != 'lightbox') {
            $html .= $this->branding_attribute();
        }

        $html .= "</div>";

        if ($this->customizer_defaults['mo_optin_branding_outside_form'] || $this->optin_campaign_type == 'lightbox') {
            $html .= $this->branding_attribute();
        }

        return $html;
    }

    /**
     * Optin form fields wrapper shortcode.
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function shortcode_optin_form_fields_wrapper($atts, $content)
    {
        $atts = shortcode_atts(
            array(
                'tag' => 'div',
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $tag = $atts['tag'];

        $class = ' ' . esc_attr($atts['class']);
        $class = "mo-optin-fields-wrapper{$class}";

        $style = '';
        if ($this->get_customizer_value('display_only_button')) {
            $style .= 'display:none;';
        }

        $style .= esc_attr($atts['style']);

        $html = "<$tag class=\"$class\" style=\"$style\">";
        $html .= do_shortcode($content);
        $html .= "</$tag>";

        return $html;
    }

    /**
     * Optin form fields wrapper shortcode.
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function shortcode_optin_form_cta_wrapper($atts, $content)
    {
        $atts = shortcode_atts(
            array(
                'tag' => 'div',
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $tag = $atts['tag'];

        $class = ' ' . esc_attr($atts['class']);
        $class = "mo-optin-form-cta-wrapper{$class}";

        $style = '';
        if ($this->get_customizer_value('display_only_button') !== true) {
            $style .= 'display:none;';
        }

        $style .= esc_attr($atts['style']);

        $html = "<$tag class=\"$class\" style=\"$style\">";
        $html .= do_shortcode($content);
        $html .= "</$tag>";

        return $html;
    }

    /**
     * HTML markup for processing and success div.
     * @return string
     */
    public function processing_success_structure()
    {
        $style = 'display:none';

        // what this does is hide cause the spinner and success message div not to be hidden by overriding the display:none style rule above.
        // the background-image: none; basically remove the spinner gif.
        if (!is_customize_preview() && OptinCampaignsRepository::user_has_successful_optin($this->optin_campaign_uuid) && $this->state_after_conversion() != 'optin_form_shown') {
            $style = 'background-image: none;'; //note: "bg none css rule" is basically only useful in processing/spinner div.
        }

        // processing / spinner div
        $html = apply_filters('mo_optin_processing_structure', "<div class='mo-optin-spinner' style='$style'></div>", $this->optin_campaign_uuid, $this->optin_css_id);

        // success div
        $html .= apply_filters(
            'mo_optin_success_structure',
            '<div class="mo-optin-success-msg" style="' . $style . '">' . $this->get_customizer_value('success_message') . '</div>',
            $this->optin_campaign_uuid,
            $this->optin_css_id
        );

        return $html;
    }

    /**
     * Plugin attribution link.
     *
     * @return mixed
     */
    public function branding_attribute()
    {
        if ($this->get_customizer_value('remove_branding') === true) {
            return '';
        }

        $affiliate_url = trim(Settings::instance()->mailoptin_affiliate_url());
        $mailoptin_url = 'https://mailoptin.io/?ref=optin-branding';

        if (!empty($affiliate_url)) $mailoptin_url = $affiliate_url;

        return apply_filters(
            'mailoptin_branding_attribute',
            "<div class=\"mo-optin-powered-by\" style='display:block !important;visibility:visible !important;position:static !important;top: 0 !important;left: 0 !important;text-align: center !important;height: auto !important;width: 60px !important;overflow: visible !important;opacity:1 !important;text-indent: 0 !important;clip: auto !important;clip-path: none !important;box-shadow:none !important;line-height:normal'>" .
            "<a style='display:block !important;visibility:visible !important;position:static !important;top: 0 !important;left: 0 !important;text-align: center !important;height: auto !important;width: 60px !important;overflow: visible !important;opacity:1 !important;text-indent: 0 !important;clip: auto !important;clip-path: none !important;box-shadow:none !important' target=\"_blank\" rel='nofollow' href=\"$mailoptin_url\">" .
            '<img src="' . MAILOPTIN_ASSETS_URL . 'images/mo-optin-brand.png" style="height:auto !important;width:60px !important;display:inline !important;visibility:visible !important;position:static !important;top: 0 !important;left: 0 !important;text-align: center !important;overflow: visible !important;opacity:1 !important;text-indent: 0 !important;clip: auto !important;clip-path: none !important;box-shadow:none !important"/>' .
            '</a></div>'
        );
    }

    /**
     * Optin form headline shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_headline($atts)
    {
        $headline = apply_filters('mo_optin_form_before_headline', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $headline .= $this->get_customizer_value('headline');
        $headline .= apply_filters('mo_optin_form_after_headline', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        $headline = do_shortcode($headline);

        $headline_styles = $this->headline_styles();

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
                'tag' => 'h2',
            ),
            $atts
        );

        $tag = sanitize_text_field($atts['tag']);
        $class = esc_attr($atts['class']);
        $class = "mo-optin-form-headline $class";

        $style = esc_attr($atts['style']);
        $style = "$headline_styles $style";

        if ($atts['tag'] == 'h2') {
            $style .= "padding: 0;";
        }

        $html = "<$tag class=\"mo-optin-form-headline $class\" style=\"$style\">$headline</$tag>";

        return $html;
    }

    /**
     * Optin form close button
     *
     * @param array $atts
     * @param mixed $content
     */
    public function shortcode_optin_form_close_button($atts, $content = '')
    {
        if ($this->get_customizer_value('hide_close_button')) return '';

        $atts = array_map('esc_attr', shortcode_atts(
                array(
                    'class' => '',
                    'style' => '',
                ),
                $atts
            )
        );

        $title = __('Close optin form');

        $close_button = "<a href='#' rel=\"moOptin:close\" title=\"$title\" class='" . $atts['class'] . "' style='" . $atts['style'] . "'>";
        $close_button .= $content;
        $close_button .= '</a>';

        return $close_button;
    }

    /**
     * Optin form headline shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_image($atts)
    {
        $atts = shortcode_atts(
            array(
                'default' => '',
                'style' => '',
            ),
            $atts
        );

        $style = esc_attr($atts['style']);

        $src = $this->get_form_image_url($atts['default']);

        return sprintf('<img src="%s" class="mo-optin-form-image" style="%s"/>', esc_url_raw($src), $style);
    }

    /**
     * Optin form headline shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_background_image($atts)
    {
        $atts = shortcode_atts(
            array(
                'default' => '',
                'style' => '',
            ),
            $atts
        );

        $style = esc_attr($atts['style']);

        $src = $this->get_form_background_image_url($atts['default_image']);

        return sprintf('<img src="%s" class="mo-optin-form-background-image" style="%s"/>', esc_url($src), $style);
    }

    /**
     * Get form_image URL.
     *
     * @param string $default_image_url
     * @return bool|false|string
     */
    public function get_form_image_url($default_image_url = '')
    {
        return $this->get_attachment_image_url('form_image', $default_image_url);
    }

    /**
     * Get form_background_image URL.
     *
     * @param string $default_image_url
     * @return bool|false|string
     */
    public function get_form_background_image_url($default_image_url = '')
    {
        $bg_image_url = $this->get_customizer_value('form_background_image');

        return !empty($bg_image_url) ? $bg_image_url : $default_image_url;
    }

    /**
     * Get attachment image url from customize image control powered setting.
     *
     * @param string $optin_form_setting
     * @param string $default_image_url
     * @return bool|false|string
     */
    public function get_attachment_image_url($optin_form_setting, $default_image_url = '')
    {
        $db_image_attachment_id = $this->get_customizer_value($optin_form_setting);

        // you could use wp_get_attachment_image_url($db_saved_form_image, '');
        // instead where second param is empty string thus returning full image url.
        return !empty($db_image_attachment_id) ? wp_get_attachment_url($db_image_attachment_id) : $default_image_url;
    }

    /**
     * Optin form description shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_description($atts)
    {
        $description = apply_filters('mo_optin_form_before_description', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $description .= $this->get_customizer_value('description');
        $description .= apply_filters('mo_optin_form_after_description', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        $description = do_shortcode($description);

        $description_styles = $this->description_styles();

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-form-description $class";

        $style = esc_attr($atts['style']);
        $style = "$description_styles $style";

        $html = "<div class=\"$class\" style=\"$style\">$description</div>";

        return $html;
    }

    /**
     * Optin form note shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_note($atts)
    {
        $note = apply_filters('mo_optin_form_before_note', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $note .= $this->get_customizer_value('note');
        $note .= apply_filters('mo_optin_form_after_note', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        $note = do_shortcode($note);

        $note_styles = $this->note_styles();

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);

        if ($this->get_customizer_value('note_close_optin_onclick')) {
            $class .= ' mo-close-optin';
        }

        $class = "mo-optin-form-note $class";

        $style = esc_attr($atts['style']);
        $style = "$note_styles $style";

        $html = "<div class=\"$class\" style=\"$style\">$note</div>";

        return $html;
    }

    /**
     * Optin form name field shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_name_field($atts)
    {
        $optin_css_id = $this->optin_css_id;
        $name_field_styles = $this->name_field_styles();
        $name_field_placeholder = $this->get_customizer_value('name_field_placeholder');

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-field mo-optin-form-name-field $class";

        $style = esc_attr($atts['style']);
        $style = "$name_field_styles $style";

        $html = apply_filters('mo_optin_form_before_form_name_field', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $html .= "<input id=\"{$optin_css_id}_name_field\" class=\"$class\" style='$style' type=\"text\" placeholder=\"$name_field_placeholder\" name=\"mo-name\" autocomplete=\"on\">";
        $html .= apply_filters('mo_optin_form_after_form_name_field', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        return $html;
    }

    /**
     * Optin form email field shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_email_field($atts)
    {
        $optin_css_id = $this->optin_css_id;
        $email_field_styles = $this->email_field_styles();
        $email_field_placeholder = $this->get_customizer_value('email_field_placeholder');

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-field mo-optin-form-email-field $class";

        $style = esc_attr($atts['style']);
        $style = "$email_field_styles $style";

        $html = apply_filters('mo_optin_form_before_form_name_field', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $html .= "<input id=\"{$optin_css_id}_email_field\" class=\"$class\" style=\"$style\" type=\"email\" placeholder=\"$email_field_placeholder\" name=\"mo-email\" autocomplete=\"on\">";
        $html .= apply_filters('mo_optin_form_after_form_email_field', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        return $html;
    }

    /**
     * Optin form submit button shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_submit_button($atts)
    {
        $optin_css_id = $this->optin_css_id;
        $submit_button_styles = $this->submit_button_styles();
        $submit_button = $this->get_customizer_value('submit_button');

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-form-submit-button $class";

        $style = esc_attr($atts['style']);
        $style = "$submit_button_styles $style";

        $html = apply_filters('mo_optin_form_before_form_submit_button', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $html .= "<input id=\"{$optin_css_id}_submit_button\" class=\"$class\" style=\"$style\" type=\"submit\" value=\"$submit_button\">";
        $html .= apply_filters('mo_optin_form_after_form_submit_button', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        return $html;
    }

    /**
     * Optin form call-to-action button shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_cta_button($atts)
    {
        $optin_css_id = $this->optin_css_id;
        $cta_button_styles = $this->cta_button_styles();
        $cta_button = $this->get_customizer_value('cta_button');

        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => ''
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-form-cta-button $class";

        $style = esc_attr($atts['style']);
        $style = "$cta_button_styles $style";

        $html = apply_filters('mo_optin_form_before_cta_button', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);
        $html .= "<input id=\"{$optin_css_id}_cta_button\" class=\"$class\" style=\"$style\" type=\"submit\" value=\"$cta_button\">";
        $html .= apply_filters('mo_optin_form_after_cta_button', '', $this->optin_campaign_id, $this->optin_campaign_type, $this->optin_campaign_uuid, $atts);

        return $html;
    }

    /**
     * Optin form error shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode_optin_form_error($atts)
    {
        $atts = shortcode_atts(
            array(
                'class' => '',
                'style' => '',
                'label' => __('Invalid email address', 'mailoptin'),
            ),
            $atts
        );

        $class = esc_attr($atts['class']);
        $class = "mo-optin-error $class";

        $style = esc_attr($atts['style']);

        $label = $atts['label'];

        $html = "<div class=\"$class\" style='$style'>$label</div>";

        return $html;
    }

    /**
     * Filter form field attributes for unofficial attributes.
     *
     * @param array $atts supplied shortcode attributes
     *
     * @return string
     */
    function mo_optin_form_other_field_atts($atts)
    {
        if (!is_array($atts)) return $atts;

        $official_atts = array('name', 'class', 'id', 'value', 'title', 'required', 'placeholder', 'key');

        $other_atts = array();

        foreach ($atts as $key => $value) {
            if (!in_array($key, $official_atts)) {
                $other_atts[$key] = $value;
            }
        }

        $other_atts_html = '';
        foreach ($other_atts as $key => $value) {
            $other_atts_html .= "$key=\"$value\" ";
        }

        return $other_atts_html;
    }
}