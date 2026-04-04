<?php

return [
    'github_repo' => env('GITHUB_REPO', 'chadw/lazeqemueditor'),
    'github_token' => env('GITHUB_TOKEN', null),
    'endpoint' => env('UPDATE_ENDPOINT', null),
    'current_version' => env('APP_VERSION') ?: (file_exists(base_path('VERSION')) ? trim(file_get_contents(base_path('VERSION'))) : '0.0.0'),
];
