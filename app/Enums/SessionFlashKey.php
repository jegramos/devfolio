<?php

namespace App\Enums;

enum SessionFlashKey: string
{
    case CMS_SUCCESS = 'cms_success';
    case CMS_ERROR = 'cms_error';
    case CMS_LOGIN_SUCCESS = 'cms_login_success';
    case CMS_EMAIL_VERIFIED = 'cms_email_verified';
}
