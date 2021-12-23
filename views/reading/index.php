<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

/**
 * @var yii\web\View $this
 * @var app\models\Content $model
 * @var yii\data\ActiveDataProvider $materialDataProvider
 * @var app\models\search\ReadingMaterialSearch $materialSearchModel
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
                    <li class="breadcrumb-item active">reading materials</li>
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
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'width' => '50px',
                        'value' => function () {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detail' => function ($model) {
                            return Yii::$app->controller->renderPartial('additionalContentDetails', [
                                'description' => $model['description'],
                            ]);
                        },
                        'headerOptions' => ['class' => 'kartik-sheet-style ficha'],
                        'contentOptions'=>['class'=>'kartik-sheet-style ficha'],
                        'expandOneOnly' => true,
                    ],
                    [
                        'attribute' => 'course.code',
                        'label' => 'Course code',
                        'value' => function($model){
                            return $model['course']['code'];
                        }
                    ],
                    [
                        'attribute' => 'course.name',
                        'label' => 'Course name',
                        'value' => function($model){
                            return $model['course']['name'];
                        }
                    ],
                    [
                        'attribute' => 'url',
                        'label' => 'Url',
                        'format' => 'raw',
                        'value' => function($model){
                            $url = $model['url'];
                            return '<a href="' . $url . '">' . $url . '</a>';
                        }
                    ],
                    [
                        'attribute' => 'creator.username',
                        'label' => 'Created by',
                        'value' => function($model){
                            return $model['creator']['username'];
                        }
                    ],
                    [
                        'attribute' => 'createdAt',
                        'label' => 'Created at',
                        'filterType' => GridView::FILTER_DATE_RANGE,
                        'format' => 'raw',
                        'vAlign' => 'middle',
                        'filterWidgetOptions' => [
                            'presetDropdown' => true,
                            'convertFormat' => true,
                            'includeMonthsFilter' => true,
                            'pluginOptions' => [
                                'locale' => ['format' => 'Y-m-d'],
                                'separator'=>' to '
                            ],
                            'options' => [
                                'id' => 'reading-created-at-date'
                            ],
                        ],
                        'value' => function($model){
                            return Yii::$app->formatter->asDatetime($model['createdAt'], 'php:d/m/y  H:i');
                        }
                    ],
                ];

                try {
                    echo GridView::widget([
                        'id' => 'reading-grid',
                        'dataProvider' => $materialDataProvider,
                        'filterModel' => $materialSearchModel,
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
                            'label' => 'Export content'
                        ],
                        'panel' => [
                            'heading' => 'Content',
                        ],
                        'persistResize' => false,
                        'toggleDataOptions' => ['minCount' => 20],
                        'itemLabelSingle' => 'content',
                        'itemLabelPlural' => 'content',
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




