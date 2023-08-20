<?php

return [
    'default' => 'Oops, something went wrong!',
    'email_already_verified' => 'Email already verified!',
    'value_must_be_email' => 'Value must be a valid email!',
    'not_found_by_email' => 'User not found by email ":email"',

    'localization' => [
        'default_language_not_set' => 'Default language not set',
        'default_language_can_be_only_one' => 'Default language can be only one',
        'first_language_must_be_active_and_default' => 'First language must be active and default',
        'at_least_one_language_must_be_active' => 'At least one language must be "active"',
        'can\'t_toggle_not_another_active_lang' => 'Can\'t switch model because there are no other active languages',
        'can\'t_disable_default_language' => 'Can\'t disable default language'
    ],
    'role' => [
        'not_found_user_role' => "User role not found",
        'not_cant_delete_role' => "The role cannot be deleted, it is bound to the user"
    ],
    'phone' => [
        'value_not_phone_instance' => 'value is not valid phone object',
        'not_found_phone' => 'Not found phone',
        'verify' => [
            'phone_already_verified' => 'Phone already verified',
            'not_valid_code' => 'Phone already verified',
            'code_has_expired' => 'Code has expired',
            'code_is_not_correct' => 'Code is not correct'
        ]
    ]
];
