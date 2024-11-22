<?php

namespace App\Enums;

use App\Enums\Traits\HasToArray;

enum ExternalLoginProvider: string
{
    use HasToArray;

    case GOOGLE = 'google';
    case GITHUB = 'github';
}
