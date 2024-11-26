<?php

namespace App\Http\Resources;

use App\Enums\ExternalLoginProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            $this->mergeWhen($this->relationLoaded('userProfile'), [
                'given_name' => $this->userProfile->given_name,
                'family_name' => $this->userProfile->family_name,
                'birthday' => $this->userProfile->birthday,
                'gender' => $this->userProfile->gender,
                'mobile_number' => $this->userProfile->mobile_number,
                'country_id' => $this->userProfile->country_id,
                'address_line_1' => $this->userProfile->address_line_1,
                'address_line_2' => $this->userProfile->address_line_2,
                'address_line_3' => $this->userProfile->address_line_3,
            ]),
            'from_external_account' => $this->isFromExternalAccount(),
            'recommend_username_change' => $this->isFromExternalAccount() && $this->recommendUsernameChange(),
        ];
    }

    private function recommendUsernameChange(): bool
    {
        // Check if the user has a default username set when
        // their account was created via an External Login provider
        $usernameArray = explode('-', $this->username);
        $usernameFirstPartIsProviderName = in_array($usernameArray[0], ExternalLoginProvider::toArray()); // Ex. 'github'
        $usernameSecondPartIsUser = isset($usernameArray[1]) && $usernameArray[1] === 'user';
        $userNeverUpdated = $this->created_at->eq($this->updated_at);

        return $usernameFirstPartIsProviderName && $usernameSecondPartIsUser && $userNeverUpdated;
    }
}
