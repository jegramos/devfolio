<?php

namespace App\Http\Controllers\User;

use App\Actions\GetCountryListAction;
use App\Actions\User\UpdateUserAction;
use App\Enums\SessionFlashKey;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ProfileController
{
    public function index(GetCountryListAction $getCountryListAction): Response
    {
        $checkAvailabilityBaseUrl = route('api.checkAvailability', ['type' => 1, 'value' => 1]);
        $checkAvailabilityBaseUrl = explode('/1/1', $checkAvailabilityBaseUrl)[0];
        return Inertia::render('Account/ProfilePage', [
            'profile' => new UserResource(Auth::user()->load('userProfile')),
            'countryOptions' => $getCountryListAction->execute('id', 'name'),
            'updateProfileUrl' => route('profile.update', Auth::user()->id),
            'checkAvailabilityBaseUrl' => $checkAvailabilityBaseUrl
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(User $user, ProfileRequest $request, UpdateUserAction $updateAction): RedirectResponse
    {
        $updateAction->execute($user, $request->validated());
        return redirect()
            ->back()
            ->with(SessionFlashKey::CMS_SUCCESS->value, 'Profile updated successfully.');
    }
}
