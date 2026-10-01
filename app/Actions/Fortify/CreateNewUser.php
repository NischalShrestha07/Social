<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'username' => $this->uniqueUsername($input['name']),
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        if (config('fortify.pre_verify_registrations')) {
            // See config/fortify.php. `email_verified_at` is deliberately not
            // fillable so it can never be set through mass assignment, and
            // forceFill skips the Verified event, which would try to send mail.
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return $user;
    }

    protected function uniqueUsername(string $name): string
    {
        $base = Str::slug($name) ?: 'user';
        $username = $base;
        $suffix = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base.$suffix++;
        }

        return $username;
    }
}
