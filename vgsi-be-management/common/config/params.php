<?php
return [
    'in_production' => false,
    'is_nam_long' => false,
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'length_password_random' => 6,
    'info_system' => [
        'logo_path' => 'https://luci.vn/wp-content/uploads/2019/11/logo.png',
        'name' => 'CÔNG TY CỔ PHẦN LUCI',
        'address' => '2nd Floor, 96A Dinh Cong St, Thanh Xuan Dist, Ha Noi',
        'tel' => '04 6259 1442 / 04 6687 0306',
        'hotline' => '0984 131 161',
        'email' => 'support@luci.vn',
        'website' => 'https://luci.vn',
    ],
    'link_api' => [
        'web' => 'https://api.staging.building.luci.vn'
    ],
    'HeaderKey' => [
        'HEADER_API_KEY' => 'X-Luci-Api-Key',
        'HEADER_LANGUAGE' => 'X-Luci-Language',
        'API_KEY_IOS' => '98CPB8ITIRGHVO3OJ5QT',
        'API_KEY_WEB' => 'Y537Z9L6IU67JVOVF5CP',
        'API_KEY_ANDROID' => 'HW8RGY3ITS8YM1BXHWZA',
    ],
    'system-api-key' => 'hqfss3mkx9qo27ys51un8vxw4d4y3vf2',
    'socket_prefix' => "socket.io",
    'Cgv_Voice_Otp' => [
        'URL_API' => 'https://voiceotp.telesip.vn/otp/request/',
        'ACCESS_TOKEN' => 'xxxxxx',
    ],
    'OneSignal' => [
        'URL_API' => 'https://onesignal.com/api/v1/',
        'APP_ID' => 'f2e6f808-23bf-47b2-948c-b0e14953707f',
        'REST_API_KEY' => 'YzkzZTc3ODYtZGU5Zi00Mjk5LWEwN2MtZDZlMWZiNWUxYjQ5',
        'USER_AUTH_KEY' => 'NDU4YjYxYTYtMmM0Yi00NzQyLTliNzEtNTgyNWZkM2EwMzQw',
    ],
    'upload' => [
        'folder' => 'uploads/',
        'maxFiles' => 100,
        'maxSize' => 26843545600,
    ],
    'rabbitmq' => [
        'base_url' => "http://127.0.0.1:15672/",
        'user' => 'admin',
        'pass' => 'luci1109',
        'vhost' => '/',
    ],
    'queueName' => [
        'SendNotify' => 'NotifyController.sendToOneSignal',
        'SendEmail' => 'EmailController.sendEmail',
        'SendEmailAws' => 'EmailController.sendEmailAws',
        'SendEmailAwsAttachments' => 'EmailController.sendEmailAwsAttachments',
        'SendSms' => 'SmsController.sendSms',
        'SendEparking' => 'IBuildingToEparking',
        'SendFaceRecognition' => 'IBuildingToFaceRecognition',
    ],
    'queueNameSend' => [
        'SendNotify' => true,
        'SendEmail' => true,
        'SendEmailAws' => true,
        'SendEmailAwsAttachments' => true,
        'SendSms' => true,
    ],
    'PhoneWhiteList' => [
        '84989528899',
        '84961196368',
        '84973404009',
        '84366564963',
        '84888729119'
    ],
    'PhoneWhiteListOtp' => [
        '84988888888',
    ],
    'EmailWhiteList' => [
        'sonlh@luci.vn',
        'lucnn@luci.vn',
        'phongtran@luci.vn',
        'duydatpham@gmail.com',
        'acc@luci.vn'
    ],
    'aws' => [
        'sender' => 'support@luci.vn',
        'config' => [
            'region_email' => 'us-east-1',
            'region_phone' => 'ap-southeast-1',
            'accessKeyId' => 'AKIAJCCZOYW6VSMGPDGA',
            'secretAccessKey' => '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',
        ]
    ],
    'WebPayment' => [
        'list_fee' => 'https://payment.staging.building.luci.vn/payment/list-by-code'
    ],
    'Apartment_form_type_list' => [
        0 => "Mini Hotel",
        1 => "Shophouse Premium",
        2 => "Shophouse Diamond",
        3 => "Shophouse Garden",
        4 => "Shophouse Opal",
        5 => "Shophouse Emerald",
        6 => "Shoptel La Cantera",
        7 => "Shophouse Sapphire",
        8 => "Shoptel Ruby",
        9 => "Shophouse The Sound",
        10 => "Villa The Soul",
        11 => "Villa The Symphony",
        12 => "Shophouse The Sea"
//        self::FORM_TYPE_1 => "Nhà phố vườn",
//        self::FORM_TYPE_2 => "Nhà phố thương mại",
//        self::FORM_TYPE_3 => "Biệt thự đơn lập",
//        self::FORM_TYPE_4 => "Biệt thự song lập"
    ],
    'Apartment_form_type_name_list' => [
        "Mini Hotel" => 0,
        "Shophouse Premium" => 1,
        "Shophouse Diamond" => 2,
        "Shophouse Garden" => 3,
        "Shophouse Opal" => 4,
        "Shophouse Emerald" => 5,
        "Shoptel La Cantera" => 6,
        "Shophouse Sapphire" => 7,
        "Shoptel Ruby" => 8,
        "Shophouse The Sound" => 9,
        "Villa The Soul" => 10,
        "Villa The Symphony" => 11,
        "Shophouse The Sea" => 12
    ],
    'Apartment_form_type_en_list' => [
        0 => "Mini Hotel",
        1 => "Shophouse Premium",
        2 => "Shophouse Diamond",
        3 => "Shophouse Garden",
        4 => "Shophouse Opal",
        5 => "Shophouse Emerald",
        6 => "Shoptel La Cantera",
        7 => "Shophouse Sapphire",
        8 => "Shoptel Ruby",
        9 => "Shophouse The Sound",
        10 => "Villa The Soul",
        11 => "Villa The Symphony",
        12 => "Shophouse The Sea"
    ],
    'PaymentConfig' => [
        'momo' => [
            'base_url' => 'https://test-payment.momo.vn',
            'request' => '/pay/app',
            'confirm' => '/pay/confirm',
            'version' => 2,
            'payType' => 3,
        ],
        'return_url' => 'https://payment.staging.building.luci.vn/payment/success',
        'cancel_url' => 'https://payment.staging.building.luci.vn/payment/cancel',
        'notify_url' => 'https://payment.staging.building.luci.vn/payment/notify'
    ],
    'NganLuong' => [
        'checkout_url' => 'https://sandbox.nganluong.vn:8088/nl35/checkout.api.nganluong.post.php',
        'checkout_url_old' => 'https://sandbox.nganluong.vn:8088/nl35/checkout.php'
    ],
    'SmsCmc' => [
        'Brandname' => 'LUCI',
        'user' => 'ctyluci',
        'pass' => 'SnYC74cU',
    ],
    'ChangeDomainWeb' => [
        'Active' => true,
        'DomainOrigin' => [
            'http://localhost:3000',
            'https://gis.meyid.net'
        ],
        'DomainOriginNew' => 'https://web.tadt.building.luci.vn'
    ],
    'mode_otp' => 'VOICE', // VOICE|SMS
    'path_root_command' => 'cd /var/www/be-management/;',
    'ConfigActionShowLog' => [
        'request' => [
            'name' => Yii::t('action-log', 'request'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
                'change-status' => Yii::t('action-log', 'change-status'),
                'add-or-remove-auth-group' => Yii::t('action-log', 'add-or-remove-auth-group'),
            ]
        ],
        'request-category' => [
            'name' => Yii::t('action-log', 'request-category'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'announcement-campaign' => [
            'name' => Yii::t('action-log', 'announcement-campaign'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
                'active-status' => Yii::t('action-log', 'active-status'),
            ]
        ],
        'announcement-category' => [
            'name' => Yii::t('action-log', 'announcement-category'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service' => [
            'name' => Yii::t('action-log', 'service'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service-building-config' => [
            'name' => Yii::t('action-log', 'service-building-config'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service-building-fee' => [
            'name' => Yii::t('action-log', 'service-building-fee'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'import' => Yii::t('action-log', 'import'),
                'delete' => Yii::t('action-log', 'delete'),
                'change-status' => Yii::t('action-log', 'change-status'),
            ]
        ],
        'service-building-info' => [
            'name' => Yii::t('action-log', 'service-building-info'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'import' => Yii::t('action-log', 'import'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service-electric-config' => [
            'name' => Yii::t('action-log', 'service-electric-config'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service-electric-fee' => [
            'name' => Yii::t('action-log', 'service-electric-fee'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'import' => Yii::t('action-log', 'import'),
                'delete' => Yii::t('action-log', 'delete'),
                'change-status' => Yii::t('action-log', 'change-status'),
            ]
        ],
        'service-electric-info' => [
            'name' => Yii::t('action-log', 'service-electric-info'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'import' => Yii::t('action-log', 'import'),
                'delete' => Yii::t('action-log', 'delete'),
                'cancel' => Yii::t('action-log', 'cancel'),
            ]
        ],
        'service-electric-level' => [
            'name' => Yii::t('action-log', 'service-electric-level'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service-management-vehicle' => [
            'name' => Yii::t('action-log', 'service-management-vehicle'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'import' => Yii::t('action-log', 'import'),
                'delete' => Yii::t('action-log', 'delete'),
                'cancel' => Yii::t('action-log', 'cancel'),
                'active' => Yii::t('action-log', 'active'),
            ]
        ],
        'service-vehicle-config' => [
            'name' => Yii::t('action-log', 'service-vehicle-config'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service-parking-fee' => [
            'name' => Yii::t('action-log', 'service-parking-fee'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'import' => Yii::t('action-log', 'import'),
                'delete' => Yii::t('action-log', 'delete'),
                'change-status' => Yii::t('action-log', 'change-status'),
            ]
        ],
        'service-parking-level' => [
            'name' => Yii::t('action-log', 'service-parking-level'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service-water-config' => [
            'name' => Yii::t('action-log', 'service-water-config'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service-water-fee' => [
            'name' => Yii::t('action-log', 'service-water-fee'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'import' => Yii::t('action-log', 'import'),
                'delete' => Yii::t('action-log', 'delete'),
                'change-status' => Yii::t('action-log', 'change-status'),
            ]
        ],
        'service-water-info' => [
            'name' => Yii::t('action-log', 'service-water-info'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'import' => Yii::t('action-log', 'import'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service-water-level' => [
            'name' => Yii::t('action-log', 'service-water-level'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'service-bill' => [
            'name' => Yii::t('action-log', 'service-bill'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
                'change-status' => Yii::t('action-log', 'change-status'),
                'block' => Yii::t('action-log', 'block'),
                'cancel' => Yii::t('action-log', 'cancel'),
                'print' => Yii::t('action-log', 'print'),
            ]
        ],
        'resident-user' => [
            'name' => Yii::t('action-log', 'resident-user'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'import' => Yii::t('action-log', 'import'),
            ]
        ],
        'apartment' => [
            'name' => Yii::t('action-log', 'apartment'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
                'import' => Yii::t('action-log', 'import'),
                'add-resident-user' => Yii::t('action-log', 'add-resident-user'),
                'remove-resident-user' => Yii::t('action-log', 'remove-resident-user'),
            ]
        ],
        'building-area' => [
            'name' => Yii::t('action-log', 'building-area'),
            'actions' => [
                'create' => Yii::t('action-log', 'create'),
                'update' => Yii::t('action-log', 'update'),
                'delete' => Yii::t('action-log', 'delete'),
            ]
        ],
        'rbac' => [
            'name' => Yii::t('action-log', 'rbac'),
            'actions' => [
                'create-auth-group' => Yii::t('action-log', 'create-auth-group'),
                'update-auth-group' => Yii::t('action-log', 'update-auth-group'),
                'auth-group-delete' => Yii::t('action-log', 'auth-group-delete'),
                'create-auth-item-web' => Yii::t('action-log', 'create-auth-item-web'),
            ]
        ],
        'auth' => [
            'name' => Yii::t('action-log', 'auth'),
            'actions' => [
                'login' => Yii::t('action-log', 'login'),
                'logout' => Yii::t('action-log', 'logout'),
                'forgot-password' => Yii::t('action-log', 'forgot-password'),
                'reset-password' => Yii::t('action-log', 'reset-password'),
            ]
        ],
    ],
    'iot_info' => [
        'url' => 'https://api.staging.region-southeast-1.iot.luci.vn',
        'token' => '1m7m9okf4v00ckbb85mva2i605kq3pfu',
        'type' => 5,
        'security' => 4
    ],
];
