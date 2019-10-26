<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder;

use MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements;
use WP_Customize_Control;

class Customizer_Control extends WP_Customize_Control
{
    public $type = 'mailoptin-email-content';

    public $elements;

    public function __construct($manager, $id, $args = array())
    {
        parent::__construct($manager, $id, $args);

        new Elements\Init();
    }

    public function saved_values()
    {
        return [
            [
                'id'       => 'text',
                'settings' => []
            ],
            [
                'id'       => 'button',
                'settings' => []
            ],
            [
                'id'       => 'image',
                'settings' => []
            ]
        ];
    }

    /**
     * Enqueue control related scripts/styles.
     *
     * @access public
     */
    public function enqueue()
    {
        wp_enqueue_script('mailoptin-customizer-email-content', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/email-content-control/control.js', array('jquery', 'customize-base'), false, true);
        wp_enqueue_style('mailoptin-customizer-email-content', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/email-content-control/style.css', null);

        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');


        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-effects-slide');
    }

    public function render_content()
    {
        $collapse_text = __('Collapse all', 'mailoptin');
        $expand_text   = __('Expand all', 'mailoptin');
        echo '<div class="mo-email-content-wrapper">';
        echo '<div class="mo-email-content-widget-wrapper">';
        printf(
            '<div class="mo-email-content-expand-collapse-wrap"><a href="#" class="mo-email-content-expand-collapse-all mo-expand" data-collapse-text="%1$s" data-expand-text="%2$s">%2$s</a></div>',
            $collapse_text, $expand_text
        );

        foreach ($this->saved_values() as $element) {
            $this->element_bar($element['id'], $element['settings']);
        }
        ?>

        <div class="mo-email-content__add_new">
            <button type="button" class="button mo-add-new-email-element">
                <?php _e('Add Element', 'mailoptin') ?>
            </button>
        </div>
        <input class="mo-email-content-save-field" id="<?= '_customize-input-' . $this->id; ?>" type="hidden" <?php $this->link(); ?>/>

        <?php
        echo '</div>';
        $this->elements_ui();
        echo '</div>';

        $this->element_settings();
    }

    public function element_bar($id, $settings)
    {
        $class = 'MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements\\' . ucfirst($id);
        /** @var AbstractElement $instance */
        $instance = call_user_func([$class, 'get_instance'])
        ?>
        <div class="mo-email-content-widget mo-email-content-part-widget element-bar" data-element-type="<?= $id ?>">
            <div class="mo-email-content-widget-top mo-email-content-part-widget-top">
                <div class="mo-email-content-part-widget-title-action">
                    <button type="button" class="mo-email-content-widget-action" data-element-type="<?= $id ?>">
                        <span class="toggle-indicator"></span>
                    </button>
                </div>
                <div class="mo-email-content-widget-title">
                    <h3><?= $instance->title() ?> <span class="mopreview">hello goalototos</span></h3>
                </div>
            </div>
        </div>
        <?php
    }

    public function element_settings()
    {
        ?>
        <div class="mo-email-content-widget mo-email-content-part-widget mo-email-content-element-settings">
            <div class="mo-email-content-go-back">
                <a href="#">&lt;&lt; <?php esc_html_e('Go Back', 'mailoptin'); ?></a>
            </div>
            <!--   settings fields get appended here by js         -->
        </div>
        <?php
    }

    public function elements_ui()
    {
        $elements = apply_filters('mo_email_content_elements', []);
        ?>
        <div class="mo-email-content-elements-wrapper">
            <div class="mo-email-content-go-back">
                <a href="#">&lt;&lt; <?php esc_html_e('Go Back', 'mailoptin'); ?></a>
            </div>
            <div class="search-form">
                <span class="screen-reader-text"><?php esc_html_e('Search elements..', 'mailoptin'); ?></span>
                <input class="search" type="search" role="search" placeholder="<?php esc_html_e('Search elements..', 'mailoptin'); ?>">
            </div>

            <ul class="list list--secondary" id="items">
                <?php foreach ($elements as $element) : ?>
                    <li class="list__item element element--box" data-element-type="<?= $element['id'] ?>">
                        <?= $element['icon'] ?>
                        <div class="element__wrap">
                            <h3 class="list__label"><?= $element['title'] ?></h3>
                            <div class="element__description"><?= $element['description'] ?></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

        </div>
        <?php
    }
}