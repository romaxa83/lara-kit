<?php

return [
    'forgot_password' => [
        'greeting' => 'Hello, :name!',
        'subject' => 'Password reset',
        'line_1' => 'There was a request to change your password!',
        'line_2' => 'If you did not make this request then please ignore this email.',
        'line_3' => 'Otherwise, please click this link to change your password: <a href=":link" class="content-link">Link</a>',
    ],

    'reset_password' => [
        'greeting' => 'Hello, :name!',
        'subject' => 'New password',
        'line_1' => 'Your new password: <strong>:password</strong>',
        'line_2' => 'Use the specified password to log into your account.',
        'line_3' => 'After entering your personal account, we strongly recommend that you change the password to a more understandable and reliable one.',
    ],

    'send_credential' => [
        'greeting' => 'Hello, :name!',
        'subject' => 'Register ' . remove_underscore(config('app.name')),
        'body' => 'You have successfully registered in your personal account',
        'login' => 'Login: :login',
        'password' => 'Password: :password',
    ]
];
