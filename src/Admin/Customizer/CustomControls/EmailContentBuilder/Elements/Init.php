<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements;


class Init
{
    public function __construct()
    {
        Text::get_instance();
        Image::get_instance();
        Button::get_instance();
        Divider::get_instance();
    }
}