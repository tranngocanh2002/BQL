<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$config = [
    'id' => 'app-pay',
    'name' => 'Urban Pay',
    'timeZone' => 'Asia/Ho_Chi_Minh',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'pay\controllers',
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'common\models\ResidentUser',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'identityCookie' => ['name' => '_identity-resident', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-pay',
            'savePath' => sys_get_temp_dir(),
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                /**
                 * @var $response \yii\web\Response
                 */
                $response = $event->sender;
                $response_format_json = (Yii::$app->params['format'] != 'json')?false:true;
                if ($response->format != \yii\web\Response::FORMAT_HTML && $response_format_json) {
                    if ($response->data !== null) {
                        $data = $response->data;
                        $response->data = [
                            'success' => $response->isSuccessful,
                            'statusCode' => $response->statusCode,
                        ];
                        if (isset($data['statusCode'])) {
                            $response->data['statusCode'] = $data['statusCode'];
                            unset($data['statusCode']);
                        }
                        if ($response->isSuccessful) {
                            if (isset($data['message'])) {
                                $response->data['message'] = $data['message'];
                                unset($data['message']);
                            }

                            if (isset($data['errors']) && isset(array_values($data['errors'])[0]) && isset(array_values($data['errors'])[0][0])) {
                                $response->data['message'] = array_values($data['errors'])[0][0];
                            }

                            if (isset($data['success'])) {
                                $response->data['success'] = $data['success'];
                                unset($data['success']);
                            }

                            $response->data['data'] = $data;
                        }
                        else {
                            if (isset($data['message'])) {
                                $response->data['message'] = $data['message'];
                            }
                            $response->data['errorCode'] = isset($data['code'])?$data['code']:-1;
                            $response->data['errorName'] = isset($data['name'])?$data['name']:"Unknown";
                        }

                        $response->statusCode = 200;
                    } else {
                        $response->data = [
                            'success' => true,
                            'statusCode' => 200,
                            'data' => []
                        ];
                    }
                }
            },
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];

if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules'] = [
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['*']
        ]
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;