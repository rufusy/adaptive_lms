<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // Google Font: Source Sans Pro
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback',
        // Font Awesome
        'adminlte/plugins/fontawesome-free/css/all.min.css',
        // iCheck
        'https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css',
        // Theme style
        'https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css',
        // Daterange picker
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css',
        // summernote
        'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css',
        // smis css
        'css/smis.css',
    ];
    public $js = [
        // daterangepicker
        'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js',
        //Summernote
        'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.js',
        // AdminLTE App
        'https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js',
        // site js
        'js/site.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset'
    ];
}
