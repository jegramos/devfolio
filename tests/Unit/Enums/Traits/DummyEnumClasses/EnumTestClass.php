<?php

namespace Tests\Unit\Enums\Traits\DummyEnumClasses;

use App\Enums\Traits\HasToArray;

enum EnumTestClass
{
    use HasToArray;

    case CASE_1;
    case CASE_2;
}