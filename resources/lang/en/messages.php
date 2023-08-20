<?php

return [
    'response' => [],
//    ''
    'forgot_password' => [
        'send' => [
            'success' => "Password reset email sent to :email"
        ],
        'greeting' => 'Hello, :name!',
        'subject' => 'Password reset',
        'line_1' => 'There was a request to change your password!',
        'line_2' => 'If you did not make this request then please ignore this email.',
        'line_3' => 'Otherwise, please click this link to change your password: <a href=":link" style="color: red">Link</a>',
    ],

    'reset_password' => [
        'action' => [
            'success' => 'Password reset successfully'
        ],
        'greeting' => 'Hello :name',
        'subject' => 'New password',
        'line_1' => 'Your new password: <strong>:password</strong>',
        'line_2' => 'Use the specified password to log into your account.',
        'line_3' => 'After entering your personal account, we strongly recommend that you change the password to a more understandable and reliable one.',
    ],

    'roles' => [
        'set-as-default-for-owner' => 'The role is set as default.',
        'cant-be-toggled' => 'Cant be toggled. Set for other role.',
    ],

    'localization' => [
        'success_set_lang' => "The user has successfully set the language",
        'translation' => [
            'actions' => [
                'install' => [
                    'success' => "Translation installed successfully",
                    'fail' => "Something went wrong while installing translations"
                ],
                'delete' => [
                    'success' => "Deleting translations was successful.",
                    'fail' => "Something went wrong while deleting translations"
                ]
            ]
        ]
    ],

    'admin' => [
        'title' => 'Admin',
        'actions' => [
            'delete' => [
                'fail' => [
                    'reasons' => [
                        'by_myself' => 'You can`t delete yourself.',
                        'super_admin' => 'You can`t delete super admin.'
                    ],
                ],
                'success' => [
                    'one_entity' => 'Deleting admin was successful.',
                    'many_entity' => 'Deleting admins was successful.',
                ],
            ],
        ],
    ],
    'user' => [
        'title' => 'User',
        'actions' => [
            'delete' => [
                'success' => [
                    'one_entity' => 'Deleting user was successful.',
                    'many_entity' => 'Deleting users was successful.',
                ],
            ],
            'change_password' => [
                'success' => 'The user\'s password has been successfully changed'
            ]
        ],
    ],
    'role' => [
        'title' => 'Role',
        'actions' => [
            'delete' => [
                'success' => [
                    'one_entity' => 'Deleting role was successful.',
                    'many_entity' => 'Deleting roles was successful.',
                ],
            ],
        ],
    ],
    'media' => [
        'avatar' => [
            'actions' => [
                'delete' => [
                    'success' => "Deleting avatar was successful."
                ]
            ]
        ]
    ],
    'phone' => [
        'sms' => [
            'verify_code' => 'You code: :code'
        ]
    ]
];
