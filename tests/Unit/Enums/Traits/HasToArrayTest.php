<?php

use Pest\Expectation;
use Tests\Unit\Enums\Traits\DummyEnumClasses\BackedEnumTestClass;
use Tests\Unit\Enums\Traits\DummyEnumClasses\EnumTestClass;
use Tests\Unit\Enums\Traits\DummyEnumClasses\NotEnumTestClass;

it('accepts and handles "value" or "name" type arguments', function (string $type) {
    $values = BackedEnumTestClass::toArray($type);
    expect($values)
        // Returns an array based on case values
        ->when($type === 'value', fn (Expectation $values) => $values->toEqual(['case_1', 'case_2']))
        // Returns an array based on case names
        ->when($type === 'name', fn (Expectation $values) => $values->toEqual(['CASE_1', 'CASE_2']));

})->with(['name', 'value']);

it('converts backed enums to array', function () {
    $values = BackedEnumTestClass::toArray();
    expect($values)->toBe(['case_1', 'case_2']);
});

it('correctly converts non-backed enums to array using "name" type', function () {
    $values = BackedEnumTestClass::toArray('name');
    expect($values)->toBe(['CASE_1', 'CASE_2']);
});

it('throws an exception for invalid type argument', function () {
    BackedEnumTestClass::toArray('invalid');
})->throws(InvalidArgumentException::class);

it('throws an exception when toArray is called on a non-enum subclass', function () {
    NotEnumTestClass::toArray();
})->throws(LogicException::class);

it('throws an exception when "value" type is used on non-backed enums', function () {
    EnumTestClass::toArray('value');
})->throws(LogicException::class);
