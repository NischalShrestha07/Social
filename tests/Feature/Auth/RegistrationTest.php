<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::registration());
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('feed', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_new_users_are_unverified_when_pre_verify_registrations_is_disabled(): void
    {
        config()->set('fortify.pre_verify_registrations', false);

        $this->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertFalse(User::where('email', 'test@example.com')->sole()->hasVerifiedEmail());
    }

    public function test_new_users_are_pre_verified_when_pre_verify_registrations_is_enabled(): void
    {
        // Local development has MAIL_MAILER=log, so the verification link never
        // arrives and every `verified` route would be unreachable.
        config()->set('fortify.pre_verify_registrations', true);

        $response = $this->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('feed', absolute: false));

        $this->assertTrue(User::where('email', 'test@example.com')->sole()->hasVerifiedEmail());
    }

    public function test_a_user_can_never_mass_assign_their_own_verification(): void
    {
        $this->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->sole();

        $this->assertNull((new User)->fill(['email_verified_at' => now()])->email_verified_at);
        $this->assertNotContains('email_verified_at', $user->getFillable());
    }
}
