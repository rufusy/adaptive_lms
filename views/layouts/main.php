<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

/* @var $this View */
/* @var $content string */

use app\assets\AppAsset;
use yii\bootstrap4\Html;
use yii\web\View;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="<?=Yii::getAlias('@web');?>/img/logo.png" type="image/x-icon">
    <link rel="icon" href="<?=Yii::getAlias('@web');?>/img/logo.png" type="image/x-icon">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<?php $this->beginBody() ?>

<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <strong><a href="<?= Url::to(['/logout']); ?>" class="nav-link">Sign out</a></strong>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <?= $this->render('./sidebar'); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <?= $content ?>
    </div>

    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2021-<?= date('Y') ?> <a href="#">Adaptive LMS</a></strong>
        All rights reserved.
    </footer>

</div>
<!-- ./wrapper -->

<?php
include_once Yii::getAlias('@views') . '/includes/growl.php';
$this->endBody()
?>
</body>
</html>
<?php $this->endPage() ?>
