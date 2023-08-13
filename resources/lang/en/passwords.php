<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'Your password has been reset!',
    'sent' => 'We have emailed your password reset link!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that email address.",

    'email' => [
        'greeting' => 'Hello, :name!',
        'registration_subject' => config('app.name').' service registration',
        'change_subject' => config('app.name').' account changes',
        'registration_success' => 'You have been successfully registered on '.config('app.name'),
        'password_changed' => 'Password to your account has been changed',
        'your_login' => 'Your login: :email',
        'password' => 'Your password to login : :password',
        'change_password' => 'We recommend to change your password after first authorization.',
        'login' => 'Login'
    ]
];
