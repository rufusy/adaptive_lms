<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@views' => '@app/views'
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'AubZdhYH9Vulup47QQ7eCsPK8O9HP6GF',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession'	=> true,
            'enableAutoLogin' => false,
            'authTimeout' => 3600
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
            'maxSourceLines' => 20,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'home' => '/site/index',
                'login' => '/site/login',
                'logout' => '/site/logout',
            ],
        ],
        'formatter' => [
            'defaultTimeZone' => 'Africa/Nairobi',
            'dateFormat' => 'd-M-Y',
            'datetimeFormat' => 'd-M-Y H:i:s'
        ],
        //
        'assetManager' => [
            /**
             * Yii loads assets from locally installed directories.
             * To improve on performance, we want to load these assets from CDNs where possible.
             */
            'appendTimestamp' => true,
            'forceCopy' => YII_DEBUG,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js',
                    ]
                ],
                'yii\jui\JuiAsset' => [
                    'css' => [
                        'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css'
                    ],
                    'js' => [
                        'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js'
                    ]
                ],
                'yii\bootstrap4\BootstrapAsset' => [
                    'css' => [
                        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css',
                    ]
                ],
                'yii\bootstrap4\BootstrapPluginAsset' => [
                    'js' => [
                        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js'
                    ],
                    'depends' => [
                        'yii\jui\JuiAsset',
                    ]
                ],

                /**
                 * Yii comes with some js assets under vendor/yiisoft/yii2/assets
                 * To improve on performance, we combine and minify these files
                 */
                'yii\web\YiiAsset' => [
                    'css' => [], 'js' => [], 'depends' => ['app\assets\AllYiiAssets']
                ],
                'yii\widgets\ActiveFormAsset' => [
                    'css' => [], 'js' => [], 'depends' => ['app\assets\AllYiiAssets']
                ],
                'yii\validators\ValidationAsset' => [
                    'css' => [], 'js' => [], 'depends' => ['app\assets\AllYiiAssets']
                ],
                'yii\grid\GridViewAsset' => [
                    'css' => [], 'js' => [], 'depends' => ['app\assets\AllYiiAssets']
                ],
                ' yii\captcha\CaptchaAsset' => [
                    'css' => [], 'js' => [], 'depends' => ['app\assets\AllYiiAssets']
                ]
            ],
        ],
    ],
    'modules' => [
        'gridview' => ['class' => 'kartik\grid\Module'],
        'datecontrol' =>  [ 'class' => '\kartik\datecontrol\Module',],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
