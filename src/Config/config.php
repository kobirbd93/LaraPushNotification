<?php
/**
 * @see https://github.com/kobirbd93/LaraPushNotification
 */

return [
    'fcm' => [
        'priority' => 'normal',
        'certificate' => __DIR__ . '/fcmCertificates/fcm-admin-sdk.json',
        'dry_run' => false,
        'firebase_project_id' => 'FIREBASE_PROJECT_ID',
        'token_cache_time' => 3000, //in seconds & must be <= 3500
        'guzzle' => [],
    ],

];
