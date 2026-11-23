<?php

return [
    'enabled' => (bool) env('PREVIEW_ACCESS_ENABLED', false),
    'username' => env('PREVIEW_ACCESS_USERNAME'),
    'password' => env('PREVIEW_ACCESS_PASSWORD'),
];
