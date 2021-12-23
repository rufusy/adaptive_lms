<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

require __DIR__ . '/../config/constants.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

/**
 * We never want to halt the application execution in production.
 * We therefore only use this method in development.
 *
 * @param $v $v [explicite description]
 *
 * @return void
 */
function dd($v) {
    if(YII_ENV_DEV) {
        \yii\helpers\VarDumper::dump($v, 10, true);
        exit();
    }
}

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
