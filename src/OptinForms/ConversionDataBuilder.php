<?php

namespace MailOptin\Core\OptinForms;

class ConversionDataBuilder
{
    public $payload;
    public $optin_uuid;
    public $optin_campaign_type;
    public $email;
    public $name;
    public $user_agent;
    public $conversion_page;
    public $referrer;
    public $connection_service;
    public $connection_email_list;
    public $is_timestamp_check_active = true;
    public $is_leadbank_active = true;
}