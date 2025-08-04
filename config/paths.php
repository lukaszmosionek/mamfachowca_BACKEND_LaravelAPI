<?php

return [
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),
    'user_messages' => env('USER_MESSAGES_PATH', '/users/{id}/messages'),
    'appointments' => env('APPOINTMENTS_PATH', '/appointments'),
];
