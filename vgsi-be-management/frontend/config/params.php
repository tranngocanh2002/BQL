<?php
return [
    'adminEmail' => 'admin@example.com',
    'white_list_phone' => [
        '0988888888', '84988888888', '0978928230'
    ],
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
    'user.passwordResetTokenExpire' => 3600,
    'jwt-config' => [
        'iss' => 'http://luci.vnn',
        'aud' => 'http://luci.vnc',
        'time' => time() + 3600 * 24 * 30,
        'time_refresh' => time() + 3600 * 24 * 90,
        'key' => 'luci123',
        'alt' => 'HS384',
    ],
    'length_password_random' => 6, //Độ dài chuỗi mật khẩu ngẫu nhiên gửi cho người dùng khi reset password
];
