<?php

return [
    'chunk_size' => 4 * 1024 * 1024,
    'max_size' => 200 * 1024 * 1024,
    'max_files' => 10,
    'expiration_hours' => 24,
    'extensions' => ['mp4', 'mov', 'm4v'],
    'mime_types' => ['video/mp4', 'video/quicktime', 'video/x-m4v'],
];
