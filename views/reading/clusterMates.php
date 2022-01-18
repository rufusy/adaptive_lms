<?php

/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

/**
 * @var yii\web\View $this
 * @var app\models\User $model
 * @var yii\data\ActiveDataProvider $clusterDataProvider
 * @var app\models\search\ClusterMatesSearch $clusterSearchModel
 * @var string $title
 */

use kartik\grid\GridView;
use yii\web\ServerErrorHttpException;

$this->title = $title;
?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">home</a></li>
                    <li class="breadcrumb-item active">my cluster mates</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12">
                <?php
                $gridColumns = [
                    ['class' => 'kartik\grid\SerialColumn'],
                    [
                        'attribute' => 'username',
                        'label' => 'Username',
                    ]
                ];

                try {
                    echo GridView::widget([
                        'id' => 'cluster-mates-grid',
                        'dataProvider' => $clusterDataProvider,
                        'filterModel' => $clusterSearchModel,
                        'columns' => $gridColumns,
                        'headerRowOptions' => ['class' => 'kartik-sheet-style grid-header'],
                        'filterRowOptions' => ['class' => 'kartik-sheet-style grid-header'],
                        'pjax' => true,
                        'toolbar' => [
                            '{export}',
                            '{toggleData}',
                        ],
                        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
                        'export' => [
                            'fontAwesome' => false,
                            'label' => 'Export cluster mates'
                        ],
                        'panel' => [
                            'heading' => 'Cluster ' . Yii::$app->user->identity->cluster,
                        ],
                        'persistResize' => false,
                        'toggleDataOptions' => ['minCount' => 20],
                        'itemLabelSingle' => 'cluster mate',
                        'itemLabelPlural' => 'cluster mates',
                    ]);
                } catch (Exception $ex) {
                    $message = 'Failed to create grid.';
                    if(YII_ENV_DEV){
                        $message = $ex->getMessage() . ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
                    }
                    throw new ServerErrorHttpException($message, 500);
                }
                ?>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
</section>
<!-- /.content -->
