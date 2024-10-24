<?php

namespace App\Enums;

enum Queue: string
{
    case DEFAULT = 'default';
    case EMAILS = 'emails';
    case NOTIFICATIONS = 'notifications';
}
