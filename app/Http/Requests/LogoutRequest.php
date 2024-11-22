<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
{
    public function rules(): array
    {
        $routeName = $this->route()->getName();

        return match ($routeName) {
            'auth.logout.otherDevices' => $this->getLogoutOtherDevicesRules(),
            default => []
        };
    }

    private function getLogoutOtherDevicesRules(): array
    {
        return [
            'password' => ['required', 'string']
        ];
    }
}
