<?php
/* @var View $this */
/* @var string $title */

use yii\web\View;

$this->title = $title;
?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="vh-100 d-flex justify-content-center align-items-center">
            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <h1><a href="#"><b>Cogni</b>Learn</a></h1>
                    </div>
                </div>
                <div class="col-12" style="padding-top: 20px;">
                    <div class="text-center">
                        <img src="<?=Yii::getAlias('@web');?>/img/brain.jpg"
                             class="img-fluid img-thumbnail">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

