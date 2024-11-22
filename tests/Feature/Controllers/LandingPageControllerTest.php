<?php

use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\get;

it('it renders the landing page', function () {
    get(route('landingPage'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('LandingPage')
                ->has('loginUrl')
                ->has('registerUrl')
        );
});
