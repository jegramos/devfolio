<?php

namespace App\Enums;

enum ErrorCode: string
{
    case INVALID_CREDENTIALS = 'INVALID_CREDENTIALS';
    case TOO_MANY_REQUESTS = 'TOO_MANY_REQUESTS';
    case EMAIL_ALREADY_VERIFIED = 'EMAIL_ALREADY_VERIFIED';
}
