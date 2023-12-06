<?php
return [
    'adminEmail' => 'admin@example.com',
    'format' => 'json',
    'settings' => [
        'name' => 'Luci',
        'title' => 'Luci',
        'description' => '',
        'hotline' => '09xxx',
        'email' => 'cskh@luci.vn',
        'website' => 'http://luci.vn',
    ],
    'agent' => -1,
    'action' => 'swagger',
    'white_list_phone' => [
        '0988888888', '84988888888', '0978928230'
    ],
    'account_kit' => [
        'app_id' => '380817722619807',
        'app_secret' => '317446e8dd50d3bd0f391abb79265678',
        'api_version' => 'v1.1',
        'url_graph' => 'https://graph.accountkit.com/',
    ],
    'length_password_random' => 6,
    'jwt-config' => [
        'iss' => 'http://luci.vnn',
        'aud' => 'http://luci.vnc',
        'time' => time() + 3600 * 24 * 30,
        'time_refresh' => time() + 3600 * 24 * 90,
        'key' => 'luci123',
        'alt' => 'HS384',
    ],
    'upload' => [
        'folder' => 'uploads/',
        'maxFiles' => 100,
        'maxSize' => 26843545600,
    ],
];
