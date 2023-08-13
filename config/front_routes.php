<?php

$frontUrl = env('FRONTEND_URL', 'http://localhost');

return [
    'home' => $frontUrl,
    'thank-you-page' => $frontUrl . '/login',
];
