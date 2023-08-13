<?php

return [
    'custom' => [
        'handlers' => [
            App\Services\AlertMessages\CustomHandlers\UserEmailVerifiedAlertMessageHandler::class,
        ],
    ],
];
