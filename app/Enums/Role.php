<?php

namespace App\Enums;

enum Role: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case SUPER_USER = 'super_user';
}
